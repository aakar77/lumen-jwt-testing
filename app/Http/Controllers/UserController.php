<?php

namespace App\Http\Controllers;

use App\User;

use Validator;
use App\Helpers\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

  private $user;
  private $request;

    public function __construct(User $user, Request $request)
      {
          $this->user = $user;
          $this->request = $request;
      }

      public function hashPassword()
      {
          // Validate the new password length...
          $this->request['password'] = Hash::make($this->request->password);
      }

      public function createUser(){

          UserController::hashPassword($this->request);

          $status = $this->user->createUser($this->request);

          if($status){
              response()->json("New User created");
          }
          else{
              response()->json("Failed to create a new user");
          }
      }
}
