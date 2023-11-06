<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory;
    use SoftDeletes;

    // 指定するテーブル名を追加
    protected $table = 'questions';

    protected $guarded = [];

    protected $fillable = [
        'project_id',
        'comment_content',
        'expected_answer_date',
        'priority',
        'status',
        'question_assignee',
        'created_user_id'
    ];
}
