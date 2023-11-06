<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Constants\GeneralConst;

class Notifications extends Notification
{
    use Queueable;

    public $user;
    public $category_id;
    public $category;
    public $project_id, $project_name, $status, $assignee_name, $created_user_name, $updated_user_name, $comment_content, $inquiry_id = null;

    /**
     * 新しい通知インスタンスを作成
     *
     * @return void
     */
    public function __construct($user, $notification_data, $notification_category)
    {
        $this->user = $user;
        $this->category = $notification_category;
        $this->category_id = $notification_data->id;
        $this->project_name = $notification_data->project_name;
        $this->status = $notification_data->status;

        if (in_array($notification_category, [GeneralConst::PROJECT_CREATE, GeneralConst::PROJECT_UPDATE])) {
            $this->assignee_name = $notification_data->assignee_name;
            $this->created_user_name = $notification_data->creator_name;
            $this->updated_user_name = $notification_data->updated_user_name;
        } elseif (in_array($notification_category, [GeneralConst::INQUIRY_CREATE, GeneralConst::INQUIRY_UPDATE])) {
            $this->comment_content = $notification_data->comment_content;
            $this->assignee_name = $notification_data->question_assignee_name;
            $this->created_user_name = $notification_data->created_user_name;
        } elseif (in_array($notification_category, [GeneralConst::COMMENT_CREATE, GeneralConst::COMMENT_EDIT])) {
            $this->project_id = $notification_data->project_id;
            $this->comment_content = $notification_data->comment_content;
            $this->assignee_name = $notification_data->comment_assignee_name;
            $this->created_user_name = $notification_data->comment_creator_name;
        } elseif (in_array($notification_category, [GeneralConst::ESTIMATE_CREATE, GeneralConst::ESTIMATE_UPDATE])) {
            $this->project_id = $notification_data->project_id;
            $this->comment_content = $notification_data->estimation_content;
            $this->assignee_name = $notification_data->estimate_assignee_name;
            $this->created_user_name = $notification_data->estimate_creator_name;
        } elseif (in_array($notification_category, [GeneralConst::ANSWER_CREATE, GeneralConst::ANSWER_UPDATE])) {
            $this->project_id = $notification_data->project_id;
            $this->comment_content = $notification_data->answer_content;
            $this->assignee_name = $notification_data->question_assignee;
            $this->created_user_name = $notification_data->created_user_name;
            $this->inquiry_id = $notification_data->inquiry_id;
        }
    }

    /**
     * 通知の配信チャネルを取得
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [NotificationsChannel::class];
    }

    /**
     * 通知の配列表現を取得
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'project_id' => $this->project_id,
            'project_name' => $this->project_name,
            'status' => $this->status,
            'assignee_name' => $this->assignee_name,
            'created_user_name' => $this->created_user_name,
            'updated_user_name' => $this->updated_user_name,
            'comment_content' => $this->comment_content,
            'inquiry_id' => $this->inquiry_id
        ];
    }
}
