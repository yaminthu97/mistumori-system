<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    // 指定するテーブル名を追加
    protected $table = 'projects';

    protected $guarded = [];

    protected $fillable = [
        'project_name',
        'customer_id',
        'project_type',
        'system_overview',
        'system_overview_file_path',
        'phases',
        'language',
        'server_env',
        'expected_dev_start_date',
        'expected_dev_end_date',
        'expected_submit_date',
        'priority',
        'status',
        'assignee',
        'created_user_id',
        'updated_user_id'
    ];
}
