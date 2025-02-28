<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'id',
        'name',
        'login',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
    ];

    public function visitor() {
        return $this->hasOne(Visitor::class, 'user_id');
    }
}
