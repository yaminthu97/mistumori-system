<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Libs\AdminAccountLib;
use App\Libs\AdminSessionLib;
use App\Libs\NotificationsLib;
use App\Constants\GeneralConst;

class NotificationsController extends Controller
{
    /**
     * @var \App\Libs\AdminSessionLib
     */
    protected $admin_session_lib;

    /**
     * @var \App\Libs\AdminAccountLib
     */
    protected $admin_account_lib;

    /**
     * @var \App\Libs\NotificationsLib
     */
    protected $notification_lib;

    private $admin_login_session_data;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            // ほとんどの画面で使いそうなクラスのインスタンスはコンストラクタで生成する
            $this->admin_session_lib = new AdminSessionLib();
            $this->admin_account_lib = new AdminAccountLib();
            $this->notification_lib = new NotificationsLib();

            // ログインユーザーのセッション情報を取得
            $this->admin_login_session_data = $this->admin_session_lib->getSessionAry();

            if (!$this->admin_login_session_data) {
                abort(400);
            }

            if (!in_array($this->admin_login_session_data['role_id'], [GeneralConst::SALES, GeneralConst::MTM])) {
                abort(200, 'E091200');
            }

            $this->admin_session_lib->setSession([
                'menu_index' => GeneralConst::ADMIN_MENU_PROJECT_MANAGEMENT
            ]);

            return $next($request);
        });
    }

    /**
     * 通知の取得
     *
     * @return JsonResponse
     */
    public function getNotifications(): JsonResponse
    {
        // 開始ログ
        $this->start();

        $notification_data = $this->notification_lib->getAllNotifications();
        $notification_data_count = $this->notification_lib->getAllNotificationsCount();

        // 終了ログ
        $this->end();
        return response()->json([$notification_data, $notification_data_count]);
    }

    /**
     * 通知を既読としてマークする
     *
     * @param $id
     * @return JsonResponse
     */
    public function markAsRead($id): JsonResponse
    {
        // 開始ログ
        $this->start();

        $this->notification_lib->pusherNotification();
        $read_at_notification = $this->notification_lib->readNotificationById($id);

        // 終了ログ
        $this->end();

        return response()->json($read_at_notification);
    }
}
