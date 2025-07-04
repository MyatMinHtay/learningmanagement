<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemRole extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'role',
        'description',
        'permissions',
        'created_at',
        'updated_at'
    ];

    public function users(){
        return $this->hasMany(User::class);
    }
}
