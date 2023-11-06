<?php

namespace App\Libs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Libs\NotificationsLib;
use App\Libs\InquiryLib;
use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use App\Models\Project;
use App\Constants\GeneralConst;

class AnswerLib
{
    /**
     * @var \App\Libs\NotificationLib
     */
    protected $notification_lib;

    /**
     * @var \App\Libs\InquiryLib
     */
    protected $inquiry_lib;

    /**
     * 新しいコントローラー インスタンスを作成
     *
     * @return void
     */
    public function __construct()
    {
        $this->notification_lib = new NotificationsLib();
        $this->inquiry_lib = new InquiryLib();
    }

    /**
     * 問い合わせIDで回答を得る
     *
     * @param $inquiry_id
     * @return Collection
     */
    public function getAnswersByInquiryId($inquiry_id): Collection
    {
        return Answer::select([
                'answers.*',
                'questions.status as inquiry_status',
                'questions.question_assignee',
                DB::raw("(SELECT users.name FROM users WHERE users.id = answers.created_user_id) AS answer_creator")
            ])
            ->leftJoin('questions', 'questions.id', 'answers.question_id')
            ->where('answers.question_id', $inquiry_id)
            ->get();
    }

    /**
     * 回答の作成と編集をする
     *
     * @param $input_data
     * @param $id
     * @return Answer
     */
    public function saveAnswer(array $input_data, $id): Answer
    {
        try {
            DB::beginTransaction();

            $answer = $id ? Answer::find($id) : new Answer();
            $answer_data = array(
                'question_id' => $input_data['inquiry_id'],
                'answer_content' => $input_data['answer_content'],
                'created_user_id' => $input_data['created_user_id'],
            );
            $answer->fill($answer_data)->save();

            $question = $this->inquiry_lib->getCreatedInquiry($input_data['inquiry_id']);

            // 質問テーブルの質問ステータスと質問担当者を更新する
            $this->updateInquiryStatusAndAssignee($input_data['inquiry_id'], $input_data['inquiry_status'], $input_data['question_assignee']);

            // ユーザーの回答作成者と質問担当者の名前を取得する
            $user = User::find($input_data['created_user_id']);
            $question_assignee_user = User::find($input_data['question_assignee']);

            $answer = $this->getAnswerById($answer->id);

            // 通知用
            $category = $id ? GeneralConst::ANSWER_UPDATE : GeneralConst::ANSWER_CREATE;
            $this->sendAnswerNotification($input_data, $question, $answer, $category);

            DB::commit();

            $answer->answer_creator         = $user->name;
            $answer->inquiry_status         = $input_data['inquiry_status'];
            $answer->question_assignee      = $input_data['question_assignee'];
            $answer->question_assignee_name = $question_assignee_user->name;
            $answer->inquiry_status_text    = trans('generalConst.inquiry_status.' . $input_data['inquiry_status']);

            return $answer;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * IDで回答を得る
     *
     * @param $id
     * @return Answer
     */
    public function getAnswerById($id): Answer
    {
        $answer         = Answer::find($id);
        $inquiry        = Question::find($answer->question_id);
        $user           = User::find($inquiry->question_assignee);
        $created_user   = User::find($answer->created_user_id);
        $project        = Project::find($inquiry->project_id);

        $answer->inquiry_status             = $inquiry->status;
        $answer->question_assignee_number   = $inquiry->question_assignee;
        $answer->question_assignee          = $user->name;
        $answer->created_user_name          = $created_user->name;
        $answer->status                     = $project->status;
        $answer->inquiry_id                 = $answer->question_id;

        return $answer;
    }

    /**
     * 問い合わせ状況を更新する
     *
     * @param $id, 
     * @param $inquiry_status, 
     * @param $question_assignee
     * @return void
     */
    public function updateInquiryStatusAndAssignee($id, $inquiry_status, $question_assignee): void
    {
        try {
            DB::beginTransaction();

            $inquiry = Question::find($id);
            $inquiry->status = $inquiry_status;
            $inquiry->question_assignee = $question_assignee;
            $inquiry->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * IDで回答を削除
     *
     * @param $id
     * @return void
     */
    public function deleteAnswerById($id): void
    {
        try {
            DB::beginTransaction();

            $answer = Answer::find($id);
            $answer->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * 回答通知を送信する
     *
     * @param array $input_data
     * @param Question $question
     * @param Answer $answer
     * @param string $category
     * @return void
     */
    public function sendAnswerNotification(array $input_data, Question $question, Answer $answer, string $category)
    {
        $notification_receivers = [
            'answer_creator'        => $input_data['created_user_id'],
            'new_answer_assignee'   => $input_data['question_assignee'],
            'question_creator'      => $question->created_user_id,
            'question_assignee'     => $question->question_assignee
        ];
        $this->notification_lib->saveNotifications($notification_receivers, $answer, $category);
    }
}
