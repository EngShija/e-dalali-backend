<?php

// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use App\Models\User;
// use App\Models\Owner;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Hash;
// use Illuminate\Validation\ValidationException;
// use Illuminate\Support\Facades\Auth; // Added for the login method

// class AuthController extends Controller
// {
//     public function register(Request $request)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'email' => 'required|string|email|max:255|unique:users',
//             'password' => 'required|string|min:8|confirmed',
//             'role' => 'required|in:customer,owner',
//             'bank_account_number' => 'required_if:role,owner|nullable|string',
//             'mobile_money_number' => 'required_if:role,owner|nullable|string',
//         ]);

//         $user = User::create([
//             'name' => $request->name,
//             'email' => $request->email,
//             'password' => Hash::make($request->password), // Changed to 'password'
//             'role' => $request->role,
//         ]);

//         if ($user->role === 'owner') {
//             Owner::create([
//                 'user_id' => $user->id,
//                 'bank_account_number' => $request->bank_account_number,
//                 'mobile_money_number' => $request->mobile_money_number,
//             ]);
//         }

//         $token = $user->createToken('auth-token')->plainTextToken;

//         return response()->json([
//             'user' => $user,
//             'token' => $token
//         ]);
//     }

//     public function login(Request $request)
//     {
//         $request->validate([
//             'email' => 'required|string|email',
//             'password' => 'required|string',
//         ]);

//         $user = User::where('email', $request->email)->first();

//         if (!$user || !Hash::check($request->password, $user->password)) { // Changed to check against 'password'
//             throw ValidationException::withMessages([
//                 'email' => ['Invalid credentials.'],
//             ]);
//         }
        
//         if ($user->is_blocked) {
//             return response()->json(['message' => 'Your account is blocked. Please clear your debt.'], 403);
//         }

//         $token = $user->createToken('auth-token')->plainTextToken;

//         return response()->json([
//             'user' => $user,
//             'token' => $token
//         ]);
//     }

//     public function logout(Request $request)
//     {
//         $request->user()->tokens()->delete();
//         return response()->json(['message' => 'Logged out successfully']);
//     }
// }
