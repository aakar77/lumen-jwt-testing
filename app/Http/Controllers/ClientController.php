<?php

namespace App\Http\Controllers;

use App\Client;

use Validator;
use App\Helpers\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{

  private $client;
  private $request;

    public function __construct(Client $client, Request $request)
      {
          $this->client = $client;
          $this->request = $request;
      }

      public function hashPassword()
      {
          // Validate the new password length...
          $this->request['c_password'] = Hash::make($this->request->c_password);
      }

      public function createClient(){

          ClientController::hashPassword($this->request);

          $status = $this->client->createClient($this->request);

          if($status){
              response()->json("New Client created");
          }
          else{
              response()->json("Failed to create a new client");
          }
      }
}
