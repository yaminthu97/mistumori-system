<?php

namespace App\Libs;

use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Mailer\Exception\TransportException;
use App\Models\Question;
use App\Constants\GeneralConst;

class InquiryLib
{
    /**
     * @var \App\Libs\NotificationsLib
     */
    protected $notification_lib;

    /**
     * @var \App\Libs\ProjectLib
     */
    protected $project_lib;

    use Notifiable;

    public function __construct()
    {
        $this->notification_lib = new NotificationsLib();

        $this->project_lib      = new ProjectLib();
    }

    /**
     * 関連するユーザーによるお問い合わせリストを取得
     *
     * @param array $search_info
     * @return Collection $query
     */
    public function getInquiryList(array $search_info = []): Collection
    {
        $select_fields = [
            'questions.*',
            'projects.project_name',
            'users.name as created_user_name',
            'user_assignee.name'
        ];

        $query = Question::select($select_fields)
            ->leftJoin('projects', 'questions.project_id', 'projects.id')
            ->leftJoin('users', 'questions.created_user_id', 'users.id')
            ->leftJoin('users as user_assignee', 'questions.question_assignee', 'user_assignee.id');

        if (isset($search_info['project_name'])) {
            $query->where('projects.project_name', 'like', '%' . addcslashes($search_info['project_name'], '%_\\') . '%');
        }

        if (isset($search_info['response_date'])) {
            $query->whereDate('questions.expected_answer_date', $search_info['response_date']);
        }

        if (isset($search_info['inquiry_status'])) {
            $query->where('questions.status', $search_info['inquiry_status']);
        }

        return $query->orderBy('priority', 'desc')->orderBy('status', 'asc')->get();
    }

    /**
     * お問い合わせレコードを作成および編集
     *
     * @param array $input_data
     * @param $id
     * @return int $inquiry->id
     */
    public function saveInquiry($input_data, $id = null): string
    {
        if ($id) {
            $old_assignee   = Question::find($id)->question_assignee;
            $category       = GeneralConst::INQUIRY_UPDATE;
        } else {
            $old_assignee   = null;
            $category       = GeneralConst::INQUIRY_CREATE;
        }

        try {
            DB::beginTransaction();

            $inquiry = $id ? Question::find($id) : (new Question())->fill(['created_user_id' => Auth::user()->id]);

            $inquiry_data = array(
                'project_id'            => $input_data['project_name'],
                'comment_content'       => $input_data['comment_content'],
                'expected_answer_date'  => $input_data['expected_answer_date'],
                'priority'              => $input_data['priority'],
                'question_assignee'     => $input_data['question_assignee'],
                'status'                => GeneralConst::NOT_STARTED
            );

            $inquiry->fill($inquiry_data)->save();

            $this->sendInquiryNotification($inquiry->id, $old_assignee, $category);

            DB::commit();
            return $inquiry->id;
        } catch (\Exception $e) {
            DB::rollBack();
            if ($e instanceof TransportException) {
                return false;
            }
            throw $e;
        }
    }

    /**
     * IDによる新しい作成されたお問い合わせを取得
     *
     * @param $id
     * @return Question|null
     */
    public function getCreatedInquiry($id): ?Question
    {
        $created_inquiry = Question::find($id);

        if ($created_inquiry) {
            $created_inquiry->project_name              = DB::table('projects')->where('id', $created_inquiry->project_id)->value('project_name');
            $created_inquiry->question_assignee_name    = DB::table('users')->where('id', $created_inquiry->question_assignee)->value('name');
            $created_inquiry->created_user_name         = DB::table('users')->where('id', $created_inquiry->created_user_id)->value('name');
        }
        return $created_inquiry;
    }

    /**
     * プロジェクトIDで質問を受け取る
     *
     * @param $project_id
     * @return Collection|null
     */
    public function getQuestionsByProjectId($project_id): ?Collection
    {
        return Question::where('project_id', $project_id)->get();
    }

    /**
     * お問い合わせ通知を送信する
     *
     * @param $id
     * @param string|null $old_assignee
     * @param string $category
     * @return void
     */
    public function sendInquiryNotification($id, ?string $old_assignee, string $category)
    {
        $inquiry_data = $this->getCreatedInquiry($id);
        $notification_receivers = [
            'inquiry_assignee'  => $inquiry_data->question_assignee,
            'old_assignee'      => $old_assignee
        ];
        $this->notification_lib->saveNotifications($notification_receivers, $inquiry_data, $category);
    }
}
