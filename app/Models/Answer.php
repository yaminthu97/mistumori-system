<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use HasFactory;
    use SoftDeletes;

    // 指定するテーブル名を追加
    protected $table = 'answers';

    protected $guarded = [];

    protected $fillable = [
        'question_id',
        'answer_content',
        'created_user_id'
    ];
}
