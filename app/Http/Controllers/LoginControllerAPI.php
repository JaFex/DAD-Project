<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

define('YOUR_SERVER_URL', 'http://dadproject.test');
// Check "oauth_clients" table for next 2 values: 
define('CLIENT_ID', '4'); 
define('CLIENT_SECRET','kQWzuTs3PxT7i251xt4thSYwFI4wgyNo5ulOOzN6');

class LoginControllerAPI extends Controller
{
    public function login(Request $request)
    {
        $active = User::where('email', $request->email)->where('deleted_at', null)->exists();
        $blocked = User::where('email', $request->email)->where('blocked', 1)->exists();

        if($blocked || !$active){
            return response()->json(['msg'=>'Access unauthorized'], 401);
        }

        $http = new \GuzzleHttp\Client;
        $response = $http->post(YOUR_SERVER_URL.'/oauth/token', [
                'form_params' => [
                'grant_type' => 'password', 
                'client_id' => CLIENT_ID, 
                'client_secret' => CLIENT_SECRET, 
                'username' => $request->email, 
                'password' => $request->password, 
                'scope' => ''
            ],
            'exceptions' => false,
        ]);
        $errorCode= $response->getStatusCode(); 
        if ($errorCode=='200') {
            return json_decode((string) $response->getBody(), true); 
        } else {
            return response()->json(['msg'=>'Invalid credentials'], $errorCode);
        }
    }

    public function logout()
    {
        \Auth::guard('api')->user()->token()->revoke(); 
        \Auth::guard('api')->user()->token()->delete();
        return response()->json(['msg'=>'Token revoked'], 200);
    }
}
