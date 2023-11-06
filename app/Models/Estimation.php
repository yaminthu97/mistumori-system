<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Estimation extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Sortable;

    // 指定するテーブル名を追加
    protected $table = 'estimations';

    protected $guarded = [];

    protected $fillable = [
        'project_id',
        'estimation_content',
        'estimation_file_path',
        'PG_man_months',
        'BSE_man_months',
        'created_user_id'
    ];
}
