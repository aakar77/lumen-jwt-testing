<?php

namespace App\Providers;

use App\Client;

use DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class AuthServiceProviderb extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request)
        {

          if($request->header('api_token'))
          {
            $api_token = $request->header('api_token');

            if($api_token)
            {

              $client = DB::table('clients')->where('c_token', '=', $api_token)->get();
              $start = Carbon::parse($client[0]->c_created);

              $now = Carbon::now();

              $length = $now->diffInHours($start);

              // Checking whether the token has expired or not.
              if($length < 2)
              {
                // Token is valid
                return $client;

              }
              /*
              else
              {
                // Token has expired
                Log::error('Error'. $length . " " . $now . " " . $start);

              } */

            }

            /*

            else
            {
              // Token is empty


            } */

          }
          /*
          else
          {
            // Header API_token not present


          } */

              //return User::where('created_at', $request->input('a'))->first();

        });
    }
}
