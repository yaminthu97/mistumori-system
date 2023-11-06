<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Http\Requests\Admin\ProjectSaveRequest;
use App\Libs\AdminAccountLib;
use App\Libs\AdminSessionLib;
use App\Libs\ProjectLib;
use App\Libs\CommentLib;
use App\Libs\EstimateLib;
use App\Libs\NotificationsLib;
use App\Libs\UserLib;
use App\Libs\InquiryLib;
use App\Libs\CustomerLib;
use App\Constants\GeneralConst;

class ProjectController extends Controller
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
     * @var \App\Libs\EstimateLib
     */
    protected $estimate_lib;

    /**
     * @var \App\Libs\NotificationsLib
     */
    protected $notification_lib;

    /**
     * @var \App\Libs\UserLib
     */
    protected $user_lib;

    /**
     * @var \App\Libs\CustomerLib
     */
    protected $customer_lib;

    /**
     * @var \App\Libs\InquiryLib
     */
    protected $inquiry_lib;

    private $admin_login_session_data;

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
            $this->estimate_lib             = new EstimateLib();
            $this->notification_lib         = new NotificationsLib();
            $this->user_lib                 = new UserLib();
            $this->inquiry_lib              = new InquiryLib();
            $this->customer_lib             = new CustomerLib();

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
     * プロジェクト内容表示 Project content display
     *
     * @param $project_id
     * @return View|RedirectResponse
     */
    public function detail($project_id): View|RedirectResponse
    {
        // 開始ログ
        $this->start();

        $project = $this->project_lib->getProjectDetailById($project_id);
        $question_count = $this->inquiry_lib->getQuestionsByProjectId($project_id)->count();

        if (!$project) {
            // 終了ログ
            $this->end();
            return redirect()->route('admin.project.index');
        }
        $users = $this->user_lib->getAllUserNames();
        $comments = $this->comment_lib->getCommentDataByProjectId($project_id);
        $estimates = $this->estimate_lib->getEstimateDataByProjectId($project_id);

        // 終了ログ
        $this->end();
        return view('admin.project.detail', compact(
            'project',
            'users',
            'comments',
            'project_id',
            'estimates',
            'question_count'
        ));
    }

    /**
     * プロジェクト一覧画面表示
     *
     * @param ProjectRequest $request
     * @return View
     */
    public function index(ProjectRequest $request): View
    {
        // 開始ログ
        $this->start();

        $admin_login_session_data = $this->admin_login_session_data;

        $search_info = $request->input();
        $projects = $this->project_lib->getProjects($search_info);

        $customers = $this->customer_lib->getCustomers();
        $users = $this->user_lib->getUsers();
        $onboarded = $this->user_lib->getUserOnboardedById();

        // 終了ログ
        $this->end();
        return view('admin.project.index', compact(
            'projects',
            'search_info',
            'users',
            'customers',
            'onboarded',
            'admin_login_session_data'));
    }

    /**
     * プロジェクトの保存
     *
     * @param ProjectSaveRequest $request
     * @param $project_id
     * @return View|RedirectResponse|JsonResponse
     */
    public function save(ProjectSaveRequest $request, $project_id = null): View|RedirectResponse|JsonResponse
    {
        // 開始ログ
        $this->start();

        $customers = $this->customer_lib->getCustomers();
        $users = $this->user_lib->getMTMUsers();

        $this->notification_lib->pusherNotification();

        $check_flg = null;
        // プロジェクトの保存
        if ($request->isMethod('post')) {
            $check_flg = $this->project_lib->saveProjects($request, $project_id);

            // 終了ログ
            $this->end();
            if (!$check_flg) {
                return redirect()->back()->withInput();
            }

            return response()->json([
                'success' => true,
                'id' => $check_flg
            ]);
        }

        $project = array();
        // プロジェクトの編集
        if (isset($project_id)) {
            $project = $this->project_lib->getProjectById($project_id);
            if (!$project) {
                // 終了ログ
                $this->end();
                return redirect()->route('admin.project.index');
            }
        }

        // 終了ログ
        $this->end();
        return view('admin.project.save', compact('customers', 'users', 'project'));
    }

    /**
     * システムコンテンツ添付ファイルのダウンロード
     *
     * @param $project_id
     * @param $filepath
     * @return void
     */
    public function systemContentDownload($project_id, $filepath = null)
    {
        //開始ログ
        $this->start();

        // プロジェクト ID がない場合は中止される
        // プロジェクト ID があり、権限がない場合、プロジェクトは中止される
        if (empty($project_id) || ($project_id && !$this->project_lib->isProjectPermission($project_id))) {
            abort(200, 'E091200');
        }

        $file_path = storage_path('app/public/system_file/' . $project_id . '/' . $filepath);
        $is_file_exist = Storage::disk('public')->exists('system_file/' . $project_id . '/' . $filepath);
        if (!$is_file_exist) {  
            abort(404, 'File not found');
        }

        //終了ログ
        $this->end();
        return response()->download($file_path);
    }
}
