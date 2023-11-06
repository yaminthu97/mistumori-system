<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Comment extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Sortable;

    // 指定するテーブル名を追加
    protected $table = 'comments';

    protected $guarded = [];

    protected $fillable = [
        'project_id',
        'comment_content',
        'comment_assignee',
        'created_user_id'
    ];
}
