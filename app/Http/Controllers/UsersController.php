<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function createUser(Request $request)
    {

        $this->validate($request, [
            'name'     => 'required',
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $request->merge([
            'password' => Hash::make($request->input('password')),
            'token'    => Str::random(60)
        ]);

        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            $model = User::create($request->all());
            
            return response()->json($model->only([
                'name',
                'email',
                'created_at',
                'token'
            ]), 201);    
        }
        
        return response()->json(['error' => 'Email is already in use'], 409); 
    }
}
