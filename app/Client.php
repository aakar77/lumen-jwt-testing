<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;


class Client extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'c_email', 'c_password',
        ];


   public function createClient($clientDataObject)
   {
       return Client::create($clientDataObject->all());
   }

   public function getJWTIdentifier()
   {
       return null;
   }

   public function getJWTCustomClaims()
   {
       return [];
   }
}
