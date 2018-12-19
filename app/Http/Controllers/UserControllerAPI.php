<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailable;
use Illuminate\Validation\Rule;

use App\User;
use App\Http\Resources\User as UserResource;
use Storage;
use Hash;
use Carbon\Carbon;
use DB;
use Redirect;

class UserControllerAPI extends Controller
{
    
    public function myProfile(Request $request)
    {
        return new UserResource($request->user());
    }

    public function store(Request $request){

        $request->validate([
            'username' => 'required|min:3|string',
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|min:3',
            'type' => 'required', Rule::in(['waiter', 'manager', 'cook', 'cashier']),
            'file' => 'required|image'
        ]);

        $user = new User();
        $user->fill($request->all());
        $user->photo_url = basename($request->file('file')->store('public/profiles'));
        $user->password = Hash::make(Carbon::now());
        $user->save();

        $tokenStr = $user->createToken('accessToken')->accessToken;

        Mail::to($request['email'])->send(new SendMailable($user->name, $tokenStr, $user->type));

        return response()->json(['msg'=>'User created'], 200);
    }

    public function verifyUser(Request $request, $token){

        $client = new \GuzzleHttp\Client();
        $response = $client->get('http://project.test/api/users/me', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
        ]);

        $errorCode= $response->getStatusCode();
        if ($errorCode != '200') {
            return view('verifyAccount', ['message' => 'Invalid user.']);
        }

        $data = json_decode((string) $response->getBody(), true);
        $user = $data['data'];

        
        $request->validate([
            'password' => 'required|min:3|string|confirmed'
        ]);

        $user['password'] = bcrypt($request['password']);
        $user['email_verified_at'] = Carbon::now();

        $userModel = User::findOrFail($user['id']);
        $userModel->update($user);

        DB::table('oauth_access_tokens')->where('user_id', $userModel->id)->delete();

        return Redirect::to('/');
    }

    public function update(Request $request, $id){
        $user = User::findOrFail($id);

        if($request->has('name') && $request['name'] != $user->name){
            $request->validate([
                'name' => 'required|min:3|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            ]);
        }

        if($request->has('name') && $request['username'] != $user->username){
            $request->validate([
                'username' => 'required|regex:/^[A-Za-z0-9]+$/|unique:users',
            ]);
        }

        if($request->has('password')){
            $request->validate([
                'password' => 'min:3|string'
            ]);
            $request['password'] = bcrypt($request['password']);
        }

        if($request->file('file')){

            if($user->photo_url != null){
                Storage::delete('public/profiles/'.$user->photo_url);
            }

            $request['photo_url'] = basename($request->file('file')->store('public/profiles'));
        }

        $user->update($request->all());
        return new UserResource($user);
    }

    public function startShift(Request $request){
        $user = $request->user();
        $user->shift_active = 1;
        $user->last_shift_start = Carbon::now();
        $user->save();
        return new UserResource(User::findOrFail($user->id));
    }

    public function endShift(Request $request){
        $user = $request->user();
        $user->shift_active = 0;
        $user->last_shift_end = Carbon::now();
        $user->save();
        return new UserResource(User::findOrFail($user->id));
    }
}
