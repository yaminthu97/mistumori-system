<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wiki extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'wikis';

    protected $guarded = [];

    protected $fillable = [
        'title',
        'content',
        'file_path',
        'modifier'
    ];
}
