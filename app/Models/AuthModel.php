<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;

class AuthModel extends Authenticatable implements JWTSubject
{
    use HasFactory;
    protected $table="AuthAdmin";
    protected $fillable=[
        'name',
        'email',
        'password',
    ];

       public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'email'=>$this->email,
        ];
    }

    public function event(){
        return $this->hasMany(Event::class,'admin_id');
    }
}
