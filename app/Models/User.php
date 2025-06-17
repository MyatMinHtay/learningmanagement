<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'role_id',
        'username',
        'email',
        'password',
        'userphoto',
        'remember_token',
        'usercreatedby',
        'userupdatedby',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(SystemRole::class);
    }


    public function hasRole($role)
    {
        return auth()->check() && auth()->user()->role->contains('name', $role);
    }


    public function getNameAttribute($value)
    {
        return ucwords($value); //mgmg => Mgmg
    }

    // table ထဲကို data ထည့်တဲ့ အခါ ကြို run ပေးတာ  
    //mutators (before data store)
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

   

}
