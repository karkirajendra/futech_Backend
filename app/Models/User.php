<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model{

protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }
};
