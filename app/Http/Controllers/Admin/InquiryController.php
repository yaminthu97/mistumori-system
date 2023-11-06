<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InquiryRequest;
use App\Libs\AdminSessionLib;
use App\Libs\InquiryLib;
use App\Libs\AnswerLib;
use App\Libs\ProjectLib;
use App\Libs\UserLib;
use App\Libs\NotificationsLib;
use App\Constants\GeneralConst;

class InquiryController extends Controller
{

    /**
     * @var \App\Libs\AdminSessionLib
     */
    protected $admin_session_lib;

    /**
     * get admin login session data
     */
    private $admin_login_session_data;

    /**
     * @var App\Libs\InquriyLib
     */
    protected $inquiry_lib;

    /**
     * @var \App\Libs\AnswerLib;
     */
    protected $answer_lib;

    /**
     * @var App\Libs\ProjectLib
     */
    protected $project_lib;

    /**
     * @var App\Libs\UserLib
     */
    protected $user_lib;

    /**
     * @var \App\Libs\NotificationsLib
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
            $this->inquiry_lib              = new InquiryLib();
            $this->answer_lib               = new AnswerLib();
            $this->project_lib              = new ProjectLib();
            $this->user_lib                 = new UserLib();
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
     * すべての問い合わせリストを取得する
     *
     * @param InquiryRequest $request
     * @return View
     */
    public function index(InquiryRequest $request): View
    {
        // 開始ログ
        $this->start();

        $admin_login_session_data = $this->admin_login_session_data;

        $search_info = $request->input();
        $inquiry_list = $this->inquiry_lib->getInquiryList($search_info);

        // 終了ログ
        $this->end();

        return view('admin.inquiry.index', compact(
            'inquiry_list',
            'search_info',
            'admin_login_session_data'
        ));
    }

    /**
     * 新しい問い合わせを作成する
     *
     * @param InquiryRequest $request
     * @param $id
     * @return View|RedirectResponse
     */
    public function save(InquiryRequest $request, $id = null): View|RedirectResponse
    {
        // 開始ログ
        $this->start();

        $created_inquiry = null;
        if ($id) {
            $created_inquiry = $this->inquiry_lib->getCreatedInquiry($id);

            if (!$created_inquiry) {
                return redirect()->route('admin.inquiry.index');
            }

            // 問い合わせ作成者がログインユーザーでない場合は拒否される
            if ($created_inquiry->created_user_id != Auth::user()->id) {
                abort(200, 'E091200');
            }

            // 問い合わせステータスが not_started 状態でない場合は拒否される
            if ($created_inquiry->status != GeneralConst::NOT_STARTED) {
                abort(400);
            }
        }

        $this->notification_lib->pusherNotification();

        // お問い合わせの作成と編集
        if ($request->isMethod('post')) {
            $created_inquiry_id = $this->inquiry_lib->saveInquiry($request->input(), $id);
            return redirect()->route('admin.inquiry.detail', ['inquiry_id' => $created_inquiry_id]);
        }

        $projects = $this->project_lib->getProjectNamesByLoginUserId();
        $users = $this->user_lib->getAllUserNames();
        $admin_login_session_data = $this->admin_login_session_data;

        // 終了ログ
        $this->end();
        return view('admin.inquiry.save', compact(
            'projects',
            'users',
            'admin_login_session_data',
            'created_inquiry'
        ));
    }

    /**
     * お問い合わせ詳細ページ、新しく作成されたお問い合わせを表示
     *
     * @param $id
     * @return view
     */
    public function showCreatedInquiry($id): View|RedirectResponse
    {
        // 開始ログ
        $this->start();

        $new_created_inquiry = $this->inquiry_lib->getCreatedInquiry($id);

        if (!$new_created_inquiry) {
            return redirect()->route('admin.inquiry.index');
        }

        $users = $this->user_lib->getAllUserNames();

        // 関連する問い合わせの回答を取得する
        $answers = $this->answer_lib->getAnswersByInquiryId($id);

        // 終了ログ
        $this->end();
        return view('admin.inquiry.detail', compact(
            'new_created_inquiry',
            'users',
            'answers'
        ));
    }
}
