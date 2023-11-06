<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EstimateRequest;
use App\Libs\AdminAccountLib;
use App\Libs\AdminSessionLib;
use App\Libs\EstimateLib;
use App\Libs\ProjectLib;
use App\Libs\NotificationsLib;

class EstimateController extends Controller
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
     * @var \App\Libs\EstimateLib
     */
    protected $estimate_lib;

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
            $this->estimate_lib             = new EstimateLib();
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
     * 見積登録
     *
     * @param EstimateRequest $request
     * @return JsonResponse
     */
    public function create(EstimateRequest $request): JsonResponse
    {
        // 開始ログ
        $this->start();

        $original_file_name = $request->file('estimation_path');
        $estimate_data = $this->estimate_lib->saveEstimateData($request->input());

        // テーブルへの推定ファイルのパスを更新し、ストレージにアップロードする
        $this->estimate_lib->uploadEstimationFile($estimate_data['id'], $original_file_name, $request['estimation_file'] ?? null);

        $estimates = $this->estimate_lib->getEstimateDataById($estimate_data['id']);

        $this->notification_lib->pusherNotification();

        // 終了ログ
        $this->end();
        return response()->json([
            'success' => true,
            'message' => 'Success Created Estimation',
            'estimates' => $estimates
        ]);
    }

    /**
     * 見積編集
     *
     * @param EstimateRequest $request
     * @return JsonResponse
     */
    public function edit(EstimateRequest $request): JsonResponse
    {
        // 開始ログ
        $this->start();

        $auth_estimate_user = $this->estimate_lib->authorizedEstimate($request['id']);
        if ($auth_estimate_user->estimate_created_user_id != Auth::user()->id) {
            return response()->json([
                'success' => false,
                'message' => Lang::get('error_msg.E091200')
            ]);
        };

        $estimate_data = $this->estimate_lib->saveEstimateData($request->input());

        $original_file_name = $request->file('estimation_path');
        if ($request['estimation_file']) {
            if (!($this->estimate_lib->getFilePath($estimate_data['id'])) || isset($original_file_name)) {
                $this->estimate_lib->uploadEstimationFile($estimate_data['id'], $original_file_name);
            } else {
                $this->estimate_lib->uploadEstimationFile($estimate_data['id']);
            }
        } else {
            // 推定ファイルをクリアし、estimate_file_path を null に設定
            $this->estimate_lib->clearEstimationFile($estimate_data['id']);
        }

        $estimates = $this->estimate_lib->getEstimateDataById($estimate_data->id);
        $this->notification_lib->pusherNotification();

        // 終了ログ
        $this->end();
        return response()->json([
            'success' => true,
            'message' => 'Success Updated Estimate',
            'estimates' => $estimates
        ]);
    }

    /**
     * 見積の削除
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request): JsonResponse
    {
        // 開始ログ
        $this->start();

        $auth_estimate_user = $this->estimate_lib->authorizedEstimate($request['id']);
        if ($auth_estimate_user->created_user_id != Auth::user()->id) {
            return response()->json([
                'success' => false,
                'message' => Lang::get('error_msg.E091200')
            ]);
        };

        // 見積データを削除する
        $this->estimate_lib->deleteEstimateData($request->id);

        // 終了ログ
        $this->end();
        return response()->json([
            'success' => true,
            'message' => 'Success Deleted Estimate'
        ]);
    }

    /**
     *  見積ファイルダウンロード
     *
     * @param $id
     * @param string|null $filepath
     * @return void
     */
    public function estimateFileDownload($id, ?string $filepath = null)
    {
        $auth_estimate_user = $this->estimate_lib->authorizedEstimate($id);

        if ($auth_estimate_user) {
            if (!in_array(Auth::user()->id,
                [
                    $auth_estimate_user->estimate_created_user_id,
                    $auth_estimate_user->project_created_user_id,
                    $auth_estimate_user->assignee
                ])) {
                abort(200, 'E091200');
            }
        }

        $file_path = storage_path('app/public/estimate_file/' . $id . '/' . $filepath);
        $is_file_exist = Storage::disk('public')->exists('estimate_file/' . $id . '/' . $filepath);

        if (!$is_file_exist) abort(404, 'File not found');

        // 終了ログ
        $this->end();
        return response()->download($file_path);
    }

    /**
     * 見積もりデータを取得する
     *
     * @param $estimate_id,
     * @return JsonResponse
     */
    public function getEstimationData($estimate_id): JsonResponse
    {
        // 開始ログ
        $this->start();

        // IDで見積もりデータを取得
        $estimation_data = $this->estimate_lib->getEstimateDataById($estimate_id);

        if (empty($estimation_data)) {
            return response()->json([
                'success' => false,
                'message' => Lang::get('error_msg.E091200')
            ]);
        }

        // 終了ログ
        $this->end();
        return response()->json([
            'success' => true,
            'message' => 'Success Get Estimate',
            'estimates' => $estimation_data
        ]);
    }
}
