<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkPageUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'vk_link',
        'vk_user_id',
    ];
}
