<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Tymon\JWTAuth\JWTAuth;
use App\Helpers\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  protected $jwt;

  public function __construct(JWTAuth $jwt, Request $request)
  {
   $this->jwt = $jwt;
   $this->request = $request;
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
    //AuthController::hashPassword($this->request);
     // Calling Validator for Validating the request

     $validatorResponse = Validator::make($this->request->all(), $rules);
     // Send failed response if validation fails

     if($validatorResponse->errors()->count()) {
          return Response::badRequest($validatorResponse->errors());
     }

     // Trying to check the token validation
     try {
         $email = $this->request->email;
         $password = $this->request->password;


         $token = $this->jwt->attempt(['email' => $email,'password'=>$password]);

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
