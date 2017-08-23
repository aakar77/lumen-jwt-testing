<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\Response;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

use Illuminate\Http\Request;

use Validator;
use Carbon\Carbon;
use DB;

use App\User;


class AuthController extends Controller
{

  public function __construct(Request $request, User $user)
  {
   $this->request = $request;
   $this->user = $user;
  }

  // Function for encrypting the password
  public function hashPassword()
  {
   // Validate the new password length...
    $this->request['password'] = Hash::make($this->request->password);
  }
    /*
  postLogin function in the Controller
  It validates password and email. If the token not found it gives Error, User not found
  */
  public function authenticate()
  {
     $rules = [
         'email' => 'required|email',
         'password' => 'required',
     ];
     // Hashing the password the user has provided
     // AuthController::hashPassword($this->request);
     // Calling Validator for Validating the request

     $validatorResponse = Validator::make($this->request->all(), $rules);
     // Send failed response if validation fails

     if($validatorResponse->errors()->count())
     {
          return Response::badRequest($validatorResponse->errors());
     }

     // Trying to check whether the user and password exists.
     try
     {

        $email = $this->request['email'];
        $password = $this->request['password'];

        $user = User::where('email', $email)->first();

        // Checking wheter user is existing or not, if yes
        if (Hash::check($password,$user['password']))
        {
          // Generating a random string here and sending that string to the user on success.

          $tokenKey = Config::get('key');
          $userToken = HASH::make(str_random(150)+$email, array('rounds'=>12));

          $current_time = Carbon::now()->toDateTimeString();

          // Storing the token into the database
          $result =  DB::table('users')
                     ->where('email',$email)
                     ->update(['user_token' => $userToken, 'token_created' => $current_time]);

          // Returning the Token to the USER
          if($result)
          {
            return $userToken;
          }
          else
          {
            // Attach a corresponding helper function
            return "Failure";
          }

        }
        else
        {
          return "Wrong Credentials";
        }



        // If the password email pair is valid, you are done

          // iF there is a valid token than in that case return that token.

          // If there is no valid token, generate the token using the secret key and save the token in the database


        // IF the passowrd and email pair is invalid send the error user not found




         if(!$token)
         {
             return response()->json(['user_not_found'], 404);
         }

     } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
         return response()->json(['token_invalid'], 500);

     } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
         return response()->json(['token_invalid'], 500);

     } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

         return response()->json(['token_absent' => $e->getMessage()], 500);
     }
     // No exception occured uptill now and so token generated, return the token back.
     /* No need to store the generated token. It will be managed internally by Laravel  */
     return response()->json(compact('token'));
  }

/* Get the authenticated user */

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }


}
