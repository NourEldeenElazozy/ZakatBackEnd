<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Register a new user
    public function register(Request $request)
    {
     $messages = [
    'name.required' => 'الاسم مطلوب.',
    'phone.required' => 'رقم الهاتف مطلوب.',
    'phone.size' => 'رقم الهاتف يجب أن يكون مكونًا من 10 أرقام.',
    'phone.unique' => 'رقم الهاتف مستخدم مسبقًا.',
    'email.required' => 'البريد الإلكتروني مطلوب.',
    'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
    'email.unique' => 'البريد الإلكتروني مستخدم مسبقًا.',
    'password.required' => 'كلمة المرور مطلوبة.',
    'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
];

$validator = Validator::make($request->all(), [
    'name' => 'required|string|max:255',
    'phone' => 'required|string|size:10|unique:users',
    'email' => 'required|string|email|max:255|unique:users',
    'password' => 'required|string|min:6',
     'device_token' => 'nullable|string', // تأكد من السماح به هنا
     
], $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
                 'email' => $request->email,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(60),
            'device_token' => $request->device_token, // تخزين رمز الجهاز
        ]);

        return response()->json(['user' => $user, 'token' => $user->api_token], 201);
    }

    // Login an existing user
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|size:10',
            'password' => 'required|string',
            
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        //$user->api_token = Str::random(60);
        $user->save();

        return response()->json(['user' => $user], 200);
    }

    // Logout the user
    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();

        if ($user) {
            $user->api_token = null;
            $user->save();
            return response()->json(['message' => 'Successfully logged out'], 200);
        }

        return response()->json(['message' => 'Unable to logout'], 401);
    }
    public function resetPassword(Request $request)
{
    $request->validate([
        'phone' => 'required',
        'password' => 'required|confirmed|min:6',
    ]);

    $user = User::where('phone', $request->phone)->first();

    if (!$user) {
        return response()->json(['message' => 'رقم الهاتف غير مسجل'], 404);
    }

    $user->password = Hash::make($request->password);
    $user->save();

    return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح'],200);
}
public function checkPhoneExists(Request $request)
{
    $request->validate([
        'phone' => 'required|string',
    ]);

    $userExists = \App\Models\User::where('phone', $request->phone)->exists();

    if ($userExists) {
        return response()->json(['exists' => true], 200);
    } else {
        return response()->json(['message' => 'رقم الهاتف غير مسجل'], 404);
    }
}
}
