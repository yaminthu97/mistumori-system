<?php

namespace App\Libs;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use App\Http\Requests\Admin\CommentRequest;
use App\Models\Comment;
use App\Constants\GeneralConst;

class CommentLib
{

    /**
     * @var \App\Libs\NotificationLib
     */
    protected $notification_lib;

    /**
     * @var \App\Libs\ProjectLib
     */
    protected $project_lib;

    public function __construct()
    {
        // ほとんどの画面で使いそうなクラスのインスタンスはコンストラクタで生成する
        $this->notification_lib = new NotificationsLib();
        $this->project_lib      = new ProjectLib();
    }

    /**
     * プロジェクトIDによるコメントデータを取得
     *
     * @param $project_id
     * @return Collection
     */
    public function getCommentDataByProjectId($project_id): Collection
    {
        $query = Comment::select([
            'comments.*',
            'users_creator.name as comment_creator_name',
            'users_creator.role as comment_creator_role',
            DB::raw("(SELECT users.name
                      FROM users
                      WHERE users.id = comments.comment_assignee) AS comment_assignee_name")
            ])
            ->leftJoin('users as users_creator', 'users_creator.id', 'comments.created_user_id')
            ->where('project_id', $project_id)->sortable();

        $comments = $query->get();
        return $comments;
    }

    /**
     * コメントIDによるデータ取得
     *
     * @param $id コメントID
     * @return Comment
     */
    public function getCommentDataById($id): Comment
    {
        $comment = Comment::select([
            'comments.*',
            'users_creator.name as comment_creator_name',
            'users_creator.role as comment_creator_role',
            DB::raw("(SELECT users.name
                    FROM users
                    WHERE users.id = comments.comment_assignee) AS comment_assignee_name"),
            DB::raw("(SELECT project_name
                    FROM projects
                    WHERE projects.id = comments.project_id) AS project_name")
            ])
            ->leftJoin('users as users_creator', 'users_creator.id', 'comments.created_user_id')
            ->where('comments.id', $id)
            ->first();

        return $comment;
    }

    /**
     * コメントを保存
     *
     * @param array $comment_data_info コメント情報
     * @return Comment
     */
    public function createCommentData(array $comment_data_info): ?Comment
    {
        try {
            DB::beginTransaction();

            $comment_data = new Comment();

            $comment_data->project_id       = $comment_data_info['project_id'];
            $comment_data->comment_assignee = $comment_data_info['comment_assignee'];
            $comment_data->comment_content  = $comment_data_info['comment_content'];
            $comment_data->created_user_id  = Auth::user()->id;
            $comment_data->save();

            $this->sendCommentNotification($comment_data, null, true);

            DB::commit();
            return $comment_data;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * コメントデータの編集
     *
     * @param CommentRequest $request, $id
     * @return Comment
     */
    public function editCommentData(CommentRequest $request): ?Comment
    {
        try {
            DB::beginTransaction();

            $comment_data = Comment::find($request['id']);

            // old_assign_receiver の通知
            $old_assign_receiver = $comment_data->comment_assignee;

            $comment_data->comment_assignee = $request->input('comment_assignee');
            $comment_data->comment_content  = $request['comment_content'];
            $comment_data->save();

            $this->sendCommentNotification($comment_data, $old_assign_receiver, false);

            DB::commit();
            return $comment_data;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * コメントデータの削除
     *
     * @param $id
     * @return bool
     */
    public function deleteCommentData($id): bool
    {
        try {
            DB::beginTransaction();

            $comment = Comment::find($id);
            $comment->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 許可されたユーザーを取得
     *
     * @param int $comment_id
     * @return Comment $auth_comment_user
     */
    function authorizedComment($comment_id): ?Comment
    {
        $auth_comment_user = Comment::find($comment_id, ['created_user_id']);
        return $auth_comment_user;
    }

    /**
     * コメント見積もり通知を送信する
     *
     * @param Comment $comment_data
     * @param string|null $old_assign_receiver
     * @param boolean $isCreate
     * @return void
     */
    public function sendCommentNotification(Comment $comment_data, ?string $old_assign_receiver, bool $isCreate = false)
    {
        $created_comment_data = $this->getCommentDataById($comment_data->id);
        $project_detail = $this->project_lib->getProjectDetailById($created_comment_data->project_id);
        $created_comment_data->status = $project_detail->status;

        // 新しく作成されたコメント通知を保存する
        $category = GeneralConst::COMMENT_CREATE;

        if (!$isCreate) {
            $notification_receivers['old_comment_assignee'] = $old_assign_receiver;
            $category =  GeneralConst::COMMENT_EDIT;
        }

        $notification_receivers = [
            'comment_creator'       => $created_comment_data->created_user_id,
            'new_comment_assignee'  => $created_comment_data->comment_assignee,
            'project_creator'       => $project_detail->created_user_id,
            'project_assignee'      => $project_detail->assignee
        ];

        $this->notification_lib->saveNotifications($notification_receivers, $created_comment_data, $category);
    }
}
