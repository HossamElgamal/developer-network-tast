<?php

namespace App\services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthService
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8',
        ]);


        $verificationCode = random_int(100000, 999999);


        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'verification_code' => $verificationCode,
        ]);


        Log::info("Verification code for {$user->phone} is: {$verificationCode}");

        return response()->json([
            'user' => $user,
            'access_token' => $user->createToken('API Token')->plainTextToken,
        ]);
    }

    public function login($data)
    {
        $validator = Validator::make($data, [
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('phone', $data['phone'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        if (!$user->is_verified) {
            return response()->json(['error' => 'Account not verified'], 403);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token], 200);
    }

    public function verifyCode(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|integer'
        ]);


        $user = User::where('phone', $request->phone)
            ->where('verification_code', $request->code)
            ->first();


        if (!$user) {
            return response()->json(['error' => 'Invalid verification code'], 401);
        }


        $user->is_verified = true;
        $user->save();

        return response()->json(['message' => 'Account verified successfully'], 200);
    }
}
