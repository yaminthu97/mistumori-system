<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AnswerRequest;
use App\Libs\AdminSessionLib;
use App\Libs\AnswerLib;
use App\Libs\NotificationsLib;
use App\Constants\GeneralConst;

class AnswerController extends Controller
{
    /**
     * @var \App\Libs\AdminSessionLib
     */
    protected $admin_session_lib;

    /**
     * 管理者ログインセッションデータを取得
     */
    private $admin_login_session_data;

    /**
     * @var \App\Libs\AnswerLib;
     */
    protected $answer_lib;

    /**
     * @var \App\Libs\InquiryLib;
     */
    protected $inquiry_lib;

    /**
     * @var \App\Libs\NotificationLib;
     */
    protected $notification_lib;

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
            $this->answer_lib               = new AnswerLib();
            $this->notification_lib         = new NotificationsLib();

            // ログインユーザーのセッション情報を取得
            $this->admin_login_session_data = $this->admin_session_lib->getSessionAry();
            if (!$this->admin_login_session_data) {
                abort(400);
            }

            $this->admin_session_lib->setSession([
                'menu_index' => GeneralConst::ADMIN_MENU_INQUIRY_MANAGEMENT
            ]);

            return $next($request);
        });
    }

    /**
     * 回答の作成と編集をする
     *
     * @param AnswerRequest $request
     * @return JsonResponse
     */
    public function save(AnswerRequest $request): JsonResponse
    {
        // 開始ログ
        $this->start();

        $id = $request->answer_id ?? null;

        if ($id && $request->created_user_id != Auth::user()->id) {

            return response()->json([
                'success' => false,
                'message' => Lang::get('error_msg.E091200')
            ]);
        }

        // 回答の作成と編集
        $answers = $this->answer_lib->saveAnswer($request->input(), $id);

        // コールプッシャー
        $this->notification_lib->pusherNotification();

        // 終了ログ
        $this->end();

        return response()->json([
            'success' => true,
            'message' => 'success created answer',
            'answers' => $answers
        ]);
    }

    /**
     * 回答を削除する
     *
     * @param Request $request, $id
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        // 開始ログ
        $this->start();

        $id = $request->answer_id;

        if ($request->created_user_id != Auth::user()->id) {
            return response()->json([
                'success' => false,
                'message' => Lang::get('error_msg.E091200')
            ]);
        }

        // IDで回答を削除
        $this->answer_lib->deleteAnswerById($id);

        // 終了ログ
        $this->end();

        return response()->json([
            'success' => true,
            'message' => 'success delete answer',
            'id' => $id
        ]);
    }

    /**
     * 回答データを取得する
     *
     * @param $id,
     * @return JsonResponse
     */
    public function getAnswerData($id)
    {
        // 開始ログ
        $this->start();

        // IDで回答データを取得
        $answer = $this->answer_lib->getAnswerById($id);

        // 終了ログ
        $this->end();

        return response()->json([
            'success' => true,
            'message' => 'success get answer',
            'answers' => $answer
        ]);
    }
}
