<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'id_users';

    protected $table = 'users';

    protected $fillable = [
        'username',
        'password',
        'nama_lengkap',
        'role',
        'email',
        'telepon',
        'status',
        'last_login',
    ];

    protected $hidden = [
        'password',
    ];

    public $timestamps = false; // <— Tambahkan ini
}