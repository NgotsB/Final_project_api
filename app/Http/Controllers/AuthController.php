<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{


    public function signup (Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'email' => 'required|string|unique:users',
            'password' => 'required|string|min:6'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        //create new user in users table
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $token = $user->createToken('Personal Access Token')->plainTextToken;
        $response = ['user' => $user, 'token' => $token];
        return response()->json($response, 200);
    }






    public function login (Request $request)
    {

        $input = $request->all();

        $validate = Validator::make ($input,[
            'email'=> 'required',
            'password'=> 'required'
        ]);

        if($validate->fails()){
            return response([
                'message'=>$validate->errors()->first(),
            ], 400);
        }

        $user = User::where('email', $input['email'])->first();

        if (!$user || !Hash::check($input['password'], $user->password)){
            return response([
                'message'=>"Your email or password is incorrect. Please try again"
            ], 401);
        }


        $token = $user->createToken('Personal Access Token')->plainTextToken;

        $response = [
            'user'=>$user,
            'token'=>$token,
        ];

        return response([
            'data' => $response
        ], 200);
    }

    public function show($id)
    {

        $user = User::find($id);
        return response([
            'message' => 'User retreived.',
            'data'=> [
                'user' => $user,
            ]
            ], 200);

    }


    public function logout()
    {
        $user = Auth::user();
        Auth::user()->tokens()->where('id', $user->id)->delete();
        return response([
            'message' => "User logout."
        ]);
    }
}
