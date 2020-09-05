<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{

    public function register(Request $request){
        $validator = Validator::make($request->json()->all() , [
            'name' => 'required|string|max:255|min:3',
            'email' => 'required|string|email|max:255|unique:users|min:8',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails())
            return response()->json($validator->errors(), 400);

        $user = User::query()->create([
            'name' => $request->json()->get('name'),
            'email' => $request->json()->get('email'),
            'password' => Hash::make($request->json()->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }

    public function login(Request $request){
        $valid = Validator::make($request->json()->all(),[
            "email"=>["required","email","string"],
            "password" => ["required","string","min:6"]
        ]);

        if($valid->fails())
            return response()->json($valid->errors(),400);

        $credentials = $request->json()->all();

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid Email Or Password'], 404);
        }

        return response()->json(compact('token'));
    }

    public function profile(){
        return response()->json(auth()->user());
    }

    public function edit(Request $request){
        $validator = Validator::make($request->json()->all() , [
            'name' => 'required|string|max:255|min:3',
            'email' => 'required|string|email|max:255|min:3'
        ]);

        if($validator->fails())
            return response()->json($validator->errors(), 400);

        $user = auth()->user()->update([
            'name' => $request->json()->get('name'),
            'email' => $request->json()->get('email'),
        ]);

        return response()->json($user);
    }

    public function edit_password(Request $request){
        $validator = Validator::make($request->json()->all() , [
            'old_password' => 'required|string',
            'new_password' => 'required|string|max:255|min:6',
        ]);

        if($validator->fails())
            return response()->json($validator->errors(), 400);

        if(!Hash::check($request->json()->get("old_password"), auth()->user()->getAuthPassword()))
            return response()->json(["message" => "Invalid old password"], 400);

        auth()->user()->update([
            "password" => Hash::make($request->json()->get('new_password'))
        ]);

        return response()->json(["message" => "Password updated"]);
    }

    public function check_email(Request $request){
            return response()->json(!User::query()->where("email","=",$request->header("email"))->get()->count() > 0);
    }

    // Manage deleting everything related to the user ......
    public function delete(){
        return response()->json(auth()->user()->delete());
    }
}
