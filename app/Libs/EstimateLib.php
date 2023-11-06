<?php

namespace App\Libs;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\Estimation;
use App\Models\Project;
use App\Constants\GeneralConst;

class EstimateLib
{
    /**
     * @var \App\Libs\NotificationsLib
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
     * 見積担当者名を取得する
     *
     * @param $project_id
     * @return Collection
     */
    public function getEstimateDataByProjectId($project_id): Collection
    {
        $query = Estimation::select([
                'estimations.*',
                'projects.status as status',
                'users_assignee.name as estimate_assignee_name',
                'users_assignee.id as user_id',
                'users_creator.name as estimate_creator_name',
                'users_creator.role as estimate_creator_role'
            ])
            ->leftJoin('projects', 'projects.id', 'estimations.project_id')
            ->leftJoin('users as users_assignee', 'users_assignee.id', 'projects.assignee')
            ->leftJoin('users as users_creator', 'users_creator.id', 'estimations.created_user_id')
            ->where('project_id', $project_id)->sortable();

        $estimates =  $query->get();
        return $estimates;
    }

    /**
     * 見積IDによるデータ取得
     *
     * @param $id 見積ID
     * @return Estimation
     */
    public function getEstimateDataById($id): Estimation
    {
        $estimation = Estimation::select([
                'estimations.*',
                'projects.status as status',
                'users_assignee.name as estimate_assignee_name',
                'users_assignee.id as user_id',
                'projects.project_name as project_name',
                'users_creator.name as estimate_creator_name',
                'users_creator.role as estimate_creator_role'
            ])
            ->leftJoin('projects', 'projects.id', 'estimations.project_id')
            ->leftJoin('users as users_assignee', 'users_assignee.id', 'projects.assignee')
            ->leftJoin('users as users_creator', 'users_creator.id', 'estimations.created_user_id')
            ->where('estimations.id', $id)
            ->first();

        return $estimation;
    }

    /**
     * 見積もりを保存する
     *
     * @param array $estimate_data お見積り情報
     * @return Estimation
     */
    public function saveEstimateData(array $estimate_data): ?Estimation
    {
        // ここでデータベース操作を実行
        DB::beginTransaction();
        try {
            $estimate = isset($estimate_data['id']) ?
                Estimation::find($estimate_data['id']) :
                (new Estimation())->fill([
                    'created_user_id' => $estimate_data['created_user_id'],
                    'project_id' => $estimate_data['project_id']
                ]);
            $estimate->estimation_content = $estimate_data['estimation_content'];
            $estimate->PG_man_months = $estimate_data['PG_man_months'];
            $estimate->BSE_man_months = $estimate_data['BSE_man_months'];
            $estimate->save();

            $old_project_assignee = $this->project_lib->getProjectAssigneeByProjectId($estimate->project_id);

            $this->project_lib->updateProject($estimate->project_id, [
                "assignee" => $estimate_data['estimate_assignee'],
                "status" => $estimate_data['status']
            ]);

            $isCreate = !isset($estimate_data['id']);

            $this->sendEstimateNotification($estimate, $old_project_assignee, $isCreate);

            DB::commit();
            return $estimate;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * 見積もりファイルをアップロードする
     *
     * @param $id
     * @param UploadedFile $original_file
     * @param $estimation_file = null
     * @return void
     */
    public function uploadEstimationFile($id, ?UploadedFile $original_file = null)
    {
        $estimate_query = Estimation::find($id);

        if ($original_file) {
            // 新しいファイルをアップロードする
            $new_file_path = $this->uploadFile($original_file, $id);
            $estimate_query->estimation_file_path = $new_file_path ?? null;
        }
        $estimate_query->save();
    }

    /**
     * ファイルをアップロードする
     *
     * @param UploadedFile $original_file
     * @param $id
     * @return string|null
     */
    public function uploadFile(?UploadedFile $original_file, $id): ?string
    {
        $file_name = GeneralConst::ESTIMATE_FILE . $id . '/';
        $original_file_name = $original_file->getClientOriginalName();
        $file_name = $file_name . $original_file_name;
        $get_file_path = $this->getFilePath($id);

        if ($get_file_path) {
            $is_file_exist = Storage::disk('public')->exists($get_file_path);
            if ($is_file_exist) Storage::disk('public')->delete($get_file_path);
        }

        Storage::disk('public')->put($file_name, file_get_contents($original_file));

        return $file_name;
    }

    /**
     * DBからIDでファイルパスを取得する
     *
     * @param int $id
     * @return null|string $file_path
     */
    public function getFilePath($id): ?string
    {
        $file_path = Estimation::find($id, ['estimation_file_path']);
        return $file_path ? $file_path->estimation_file_path : null;
    }

    /**
     * 見積データの削除
     *
     * @param $id
     * @return bool
     */
    public function deleteEstimateData($id): bool
    {
        try {
            DB::beginTransaction();

            $estimate = Estimation::find($id);

            $file_path = $estimate->estimation_file_path;
            if ($file_path && Storage::disk('public')->exists($file_path)) {
                $folder_path = dirname($file_path);
                Storage::disk('public')->deleteDirectory($folder_path);
            }

            $estimate->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 許可されたユーザーを取得する
     *
     * @param int $estimate_id
     * @return Estimation|null $auth_estimate_user
     */
    function authorizedEstimate($estimate_id): ?Estimation
    {
        $auth_estimate_user = Estimation::select([
                'estimations.created_user_id as estimate_created_user_id',
                'projects.created_user_id as project_created_user_id',
                'projects.assignee'
            ])
            ->leftJoin('projects', 'projects.id', 'estimations.project_id')
            ->where('estimations.id', $estimate_id)
            ->first();
        return $auth_estimate_user;
    }

    /**
     * 見積もりファイルをクリアする
     *
     * @param $id
     * @return void
     */
    public function clearEstimationFile($id): void
    {
        $estimate_query = Estimation::find($id);

        $get_file_path = $this->getFilePath($id);
        if ($get_file_path && Storage::disk('public')->exists($get_file_path)) {
            $folder_path = dirname($get_file_path);
            Storage::disk('public')->deleteDirectory($folder_path);
        }

        $estimate_query->estimation_file_path = null;
        $estimate_query->save();
    }

    /**
     * 見積もり通知を送信する
     *
     * @param Estimation $estimate
     * @param string $old_project_assignee
     * @return void
     */
    public function sendEstimateNotification(Estimation $estimate, ?string $old_project_assignee, bool $isCreate = false): void
    {
        $project = Project::find($estimate->project_id);

        $category = $isCreate ? GeneralConst::ESTIMATE_CREATE : GeneralConst::ESTIMATE_UPDATE;
        $estimate_data = $this->getEstimateDataById($estimate->id);

        $notification_receivers = [
            'project_creator' => $project->created_user_id,
            'old_project_assignee' => $old_project_assignee,
            'new_project_assignee' => $project->assignee
        ];

        $this->notification_lib->saveNotifications($notification_receivers, $estimate_data, $category);
    }
}
