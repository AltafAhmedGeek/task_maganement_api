<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        $rules = [
            'email'=>['required','email'],
            'password'=>['required','string']
        ];
        $validator = Validator::make($credentials, $rules);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation Error(s).',
                'error' => $validator->errors(),
            ]);
        }
        $token = JWTAuth::attempt($credentials);
        if(!$token){
            return response()->json([
                'status' => false,
                'message' => 'Login Failed.',
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => 'Login Successfully.',
            'token' => $token,
        ]);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
