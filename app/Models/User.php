<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id';

    // 指定するテーブル名を追加
    protected $table = 'users';

    protected $guarded = [];

    public function projects()
    {
        return $this->hasMany('Project', 'assignee');
    }
}
