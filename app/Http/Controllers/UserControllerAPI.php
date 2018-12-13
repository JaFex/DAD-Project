<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailable;

use App\User;
use App\Http\Resources\User as UserResource;
use Storage;

class UserControllerAPI extends Controller
{
    
    public function myProfile(Request $request)
    {
        return new UserResource($request->user());
    }

    public function store(Request $request){
        Mail::to($request['email'])->send(new SendMailable($request['name'], 'xpto'));
        return $request;
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
}
