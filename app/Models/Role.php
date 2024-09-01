<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillables = [
        'name'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'id'
    ];


    public function users(){
        return $this->hasMany(User::class);
    }
}
