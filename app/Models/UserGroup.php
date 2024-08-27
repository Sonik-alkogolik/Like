<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;

    protected $table = 'user_groups';

    protected $fillable = [
        'user_id_vk',
        'group_id',
        'group_link', 
    ];

}