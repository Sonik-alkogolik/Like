<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VkPageUser extends Model
{
    use HasFactory;
    protected $table = 'vk_page_users';
    protected $fillable = [
        'id',
        'user_id',
        'vk_link',
        'vk_user_id',
    ];
}
