<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    // 指定するテーブル名を追加
    protected $table = 'customers';

    protected $guarded = [];

    protected $fillable = [
        'customer_name',
        'description',
        'status',
        'created_user_id',
        'updated_user_id'
    ];
}
