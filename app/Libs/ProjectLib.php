<?php

namespace App\Libs;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use App\Models\Project;
use App\Libs\NotificationsLib;
use App\Constants\GeneralConst;
use App\Http\Requests\Admin\ProjectSaveRequest;

/**
 * プロジェクト情報処理クラス
 */
class ProjectLib
{
    /**
     * @var \App\Libs\NotificationsLib
     */
    protected $notification_lib;

    use Notifiable;

    public function __construct()
    {
        $this->notification_lib = new NotificationsLib();
    }

    /**
     * プロジェクトのIDによって詳細を取得
     *
     * @param $project_id
     * @return Project
     */
    public function getProjectDetailById($project_id): Project
    {
        $query = Project::select([
            'projects.*',
            DB::raw("(SELECT customers.customer_name
                    FROM customers
                    WHERE customers.id = projects.customer_id) AS customer_name"),
            DB::raw("(SELECT users.name
                    FROM users
                    WHERE users.id = projects.assignee) AS assignee_name"),
            DB::raw("(SELECT users.name
                    FROM users
                    WHERE users.id = projects.created_user_id) AS creator_name"),
            DB::raw("(SELECT users.name
                    FROM users
                    WHERE users.id = projects.updated_user_id) AS updated_user_name")
        ])->where('projects.id', $project_id);

        $project =  $query->first();
        return $project;
    }

    /**
     * プロジェクトの取得
     *
     * @param array $projects
     * @return Collection
     */
    public function getProjects(array $projects): ?Collection
    {
        $select_fields = [
            'projects.*',
            'customers.customer_name',
            'assignee_users.name as assignee',
            'created_users.name as created_user_name'
        ];

        $query = Project::select($select_fields)
            ->leftJoin('customers', 'customers.id', 'projects.customer_id')
            ->leftJoin('users as assignee_users', 'assignee_users.id', 'projects.assignee')
            ->leftJoin('users as created_users', 'created_users.id', 'projects.created_user_id')
            ->orderByDesc('projects.priority')
            ->orderBy('projects.status');

        if (isset($projects['project_name'])) {
            $query->where('projects.project_name', 'like', '%' . addcslashes($projects['project_name'], '%_\\') . '%');
        }

        if (isset($projects['customer_name'])) {
            $query->where('customers.customer_name', $projects['customer_name']);
        }

        if (isset($projects['assignee'])) {
            $query->where('assignee_users.name', $projects['assignee']);
        }

        if (isset($projects['status'])) {
            $query->where('projects.status', $projects['status']);
        }

        if (isset($projects['submit_date'])) {
            $query->whereDate('projects.expected_submit_date', date('Y-m-d', strtotime($projects['submit_date'])));
        }

        if (isset($projects['priority'])) {
            $query->whereIn('projects.priority', $projects['priority']);
        }

        $projects = $query->get();
        return $projects;
    }

    /**
     * 許可されたユーザーを取得
     *
     * @param $project_id
     * @return bool
     */
    public function isProjectPermission($project_id): bool
    {
        $authorized_users = Project::select('projects.assignee', 'projects.created_user_id')
            ->where('id', $project_id)
            ->first();

        if (!$authorized_users) return false;

        return in_array(Auth::user()->id, [$authorized_users->assignee, $authorized_users->created_user_id]);
    }

    /**
     * プロジェクトの更新
     *
     * @param int $project_id
     * @param array $data
     * @return bool
     */
    public function updateProject($project_id, array $data): bool
    {
        $project = Project::find($project_id);
        if (!$project) {
            return false;
        }

        $project->assignee  = $data['assignee'];
        $project->status    = $data['status'];

        return $project->save();
    }

    /**
     * 関連するユーザーのプロジェクトリストを取得
     *
     * @return array
     */
    public function getProjectNamesByLoginUserId(): array
    {
        return DB::table('projects')
            ->select('id', 'project_name')
            ->distinct()
            ->get()->toArray();
    }

    /**
     * プロジェクトの保存
     *
     * @param ProjectSaveRequest $request
     * @param $id
     * @return int
     */
    public function saveProjects(ProjectSaveRequest $request, $id): int
    {
        $projects           = $request->input();
        $original_file_name = $request->file('system_path');

        if ($id) {
            $old_assignee       = Project::find($id)->assignee;
            $category           = GeneralConst::PROJECT_UPDATE;
        } else {
            $old_assignee       = null;
            $category           = GeneralConst::PROJECT_CREATE;
        }

        try {
            DB::beginTransaction();

            $project = $id ? Project::find($id) : (new Project())->fill(['created_user_id' => Auth::user()->id]);
            // DBに保存
            $project_data = array(
                'project_name'              => $projects['project_name'],
                'customer_id'               => $projects['customer_id'],
                'project_type'              => $projects['project_type'],
                'system_overview'           => $projects['system_content'],
                'phases'                    => $projects['development_process'],
                'language'                  => $projects['development_language'],
                'server_env'                => $projects['server_environment'],
                'expected_dev_start_date'   => $projects['development_start_date'],
                'expected_dev_end_date'     => $projects['development_end_date'],
                'expected_submit_date'      => $projects['submit_date'],
                'priority'                  => $projects['priority'],
                'status'                    => $projects['status'],
                'assignee'                  => $projects['assignee'],
                'updated_user_id'           => Auth::user()->id
            );

            $project->fill($project_data);
            $project->save();

            //upload file in project create/edit 
            if(isset($projects['system_file'])) {
                $file_name = GeneralConst::SYSTEM_FILE_FOLDER . $project->id . '/' . $project['system_file'];
                $system_overview_file_path = $this->projectFilesControl($project->id, $file_name, $original_file_name);
            } else {
                $this->deleteFile($project->id);
            }

            // update system_overview_file_path
            Project::where('id', $project->id)->update(['system_overview_file_path' => $system_overview_file_path ?? null]);

            //send notification
            $this->sendProjectNotification($project->id, $old_assignee, $category);

            DB::commit();
            return $project->id;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * IDでプロジェクトを取得
     *
     * @param $id
     * @return Object
     */
    public function getProjectById($id): ?Object
    {
        return Project::find($id);
    }

    /**
     * プロジェクトファイルのアップロードを制御
     *
     * @param $id
     * @param string|null $file_name
     * @param UploadedFile|null $original_file_name
     * @return string
     */
    public function projectFilesControl($id, ?string $file_name, ?UploadedFile $original_file_name): string
    {
        return !Storage::disk('public')->exists($file_name) || isset($original_file_name) ? 
            $this->updateFile($original_file_name, $id) : $file_name;
    }

    /**
     * アップデートファイル
     *
     * @param UploadedFile|null $original_file_name
     * @param $id
     * @return string
     */
    public function updateFile(?UploadedFile $original_file_name, $id): string
    {
        $file_name              = GeneralConst::SYSTEM_FILE_FOLDER . $id;
        $system_file_uploaded   = $this->uploadFile($original_file_name, $file_name, $id);

        return $system_file_uploaded['save_file_name'];
    }

    /**
     * ファイルをアップロードする
     *
     * @param UploadedFile|null $file_path
     * @param string|null $file_name
     * @param $id
     * @return array
     */
    public function uploadFile(?UploadedFile $file_path, ?string $file_name, $id): array
    {
        $file_name          = $file_name . '/';
        $original_file_name = $file_path->getClientOriginalName();
        $file_name          = $file_name . $original_file_name;

        // データベースから古いファイルパスを取得
        $get_file_path = $this->getFilePath($id);

        // チェックされた古いファイルは、データベースとストレージパスに存在
        if ($get_file_path && Storage::disk('public')->exists($get_file_path)) {
            Storage::disk('public')->delete($get_file_path);
        }

        Storage::disk('public')->put($file_name, file_get_contents($file_path));

        $file_name_array = array(
            'original_file_name'    => $original_file_name,
            'save_file_name'        => $file_name,
        );
        return $file_name_array;
    }

    /**
     * IDによってDBからファイルパスを取得
     *
     * @param $id
     * @return string
     */
    public function getFilePath($id): ?string
    {
        $file_path = Project::select('system_overview_file_path')
            ->where('id', $id)
            ->first();

        return $file_path->system_overview_file_path;
    }

    /**
     * IDでファイルを削除
     *
     * @param $id
     * @return null
     */
    public function deleteFile($id)
    {
        $get_file_path = $this->getFilePath($id);
        if ($get_file_path) {
            if (Storage::disk('public')->exists($get_file_path)) {
                Storage::disk('public')->delete($get_file_path);
                Storage::disk('public')->deleteDirectory(GeneralConst::SYSTEM_FILE_FOLDER . $id);
            }
        }
        return null;
    }

    /**
     * プロジェクト通知を送信
     *
     * @param $id
     * @param string|null $old_assignee
     * @param string $category
     * @return void
     */
    public function sendProjectNotification($id, ?string $old_assignee, string $category) {
        $created_project = $this->getProjectDetailById($id);
        $notification_receivers = [
            'project_creator'   => $created_project->created_user_id,
            'project_assignee'  => $created_project->assignee,
            'old_assignee'      => $old_assignee
        ];
        $this->notification_lib->saveNotifications($notification_receivers, $created_project, $category);
    }

    /**
     * IDでプロジェクト譲受人を取得
     *
     * @param $project_id
     * @return string
     */
    public function getProjectAssigneeByProjectId($project_id): ?string
    {
        $project = Project::find($project_id, ['assignee']);
        return $project->assignee;
    }
}
