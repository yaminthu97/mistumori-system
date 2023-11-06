<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CommentRequest;
use App\Libs\AdminAccountLib;
use App\Libs\AdminSessionLib;
use App\Libs\CommentLib;
use App\Libs\NotificationsLib;
use App\Libs\ProjectLib;

class CommentController extends Controller
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
     * @var \App\Libs\ProjectLib
     */
    protected $project_lib;

    /**
     * @var \App\Libs\CommentLib
     */
    protected $comment_lib;

    /**
     * @var \App\Libs\NotificationsLib
     */
    protected $notification_lib;

    protected $admin_login_session_data;

    /**
     * 新しいコントローラー インスタンスを作成
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            // ほとんどの画面で使いそうなクラスのインスタンスはコンストラクタで生成する
            $this->admin_session_lib        = new AdminSessionLib();
            $this->admin_account_lib        = new AdminAccountLib();
            $this->project_lib              = new ProjectLib();
            $this->comment_lib              = new CommentLib();
            $this->notification_lib         = new NotificationsLib();

            // ログインユーザーのセッション情報を取得
            $this->admin_login_session_data = $this->admin_session_lib->getSessionAry();

            if (!$this->admin_login_session_data) {
                abort(400);
            }

            return $next($request);
        });
    }

    /**
     * コメント登録
     *
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function create(CommentRequest $request): JsonResponse
    {
        // 開始ログ
        $this->start();

        $comment_data = $this->comment_lib->createCommentData($request->input());
        $comments = $this->comment_lib->getCommentDataById($comment_data['id']);

        $this->notification_lib->pusherNotification();

        // 終了ログ
        $this->end();
        return response()->json([
            'success' => true,
            'message' => 'Success Created Comment',
            'comments' => $comments
        ]);
    }

    /**
     * コメント編集
     *
     * @param CommentRequest $request
     * @return JsonResponse
     */
    public function edit(CommentRequest $request): JsonResponse
    {
        // 開始ログ
        $this->start();

        $auth_comment_user = $this->comment_lib->authorizedComment($request['id']);
        if ($auth_comment_user->created_user_id != Auth::user()->id) {
            return response()->json([
                'success' => false,
                'message' => Lang::get('error_msg.E091200')
            ]);
        };

        $comment_data = $this->comment_lib->editCommentData($request);
        $comments = $this->comment_lib->getCommentDataById($comment_data->id);

        $this->notification_lib->pusherNotification();

        // 終了ログ
        $this->end();
        return response()->json([
            'success' => true,
            'message' => 'Success Updated Comment',
            'comments' => $comments
        ]);
    }

    /**
     * コメントの削除
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        // 開始ログ
        $this->start();

        $auth_comment_user = $this->comment_lib->authorizedComment($request['id']);
        if ($auth_comment_user->created_user_id != Auth::user()->id) {
            abort(200, 'E091200');
        };

        // コメントを削除
        $this->comment_lib->deleteCommentData($request->id);

        // 終了ログ
        $this->end();
        return redirect()->back();
    }
}
