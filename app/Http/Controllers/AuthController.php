<?php

namespace App\Http\Controllers;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use OpenApi\Annotations as OA;


class AuthController extends Controller
{
    use HttpResponses;

        /**
     * @OA\POST(
     *     path="/register",
     *     summary="Register a new user",
     *     parameters={
     *         @OA\Parameter(name="name", in="query", required=true, @OA\Schema(type="string")),
     *         @OA\Parameter(name="email", in="query", required=true, @OA\Schema(type="string")),
     *         @OA\Parameter(name="password", in="query", required=true, @OA\Schema(type="string")),
     *         @OA\Parameter(name="password_confirmation", in="query", required=true, @OA\Schema(type="string")),
     *     },
     *     tags={"Auth"},
     *     @OA\Response(response=201, description="User registered successfully"),
     *     @OA\Response(response=400, description="Invalid request")
     * )
     */

    public function register(Request $request, AuthRepositoryInterface $authRepository)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $authRepository->createUser([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['data' => $user, 'access_token' => $token, 'token_type' => 'Bearer'], 201);
    }

        /**
     * @OA\POST(
     *     path="/login",
     *     summary="Login a user",
     *     parameters={
     *         @OA\Parameter(name="email", in="query", required=true, @OA\Schema(type="string")),
     *         @OA\Parameter(name="password", in="query", required=true, @OA\Schema(type="string")),
     *     },
     *     tags={"Auth"},
     *     @OA\Response(response=200, description="Login successful"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function login(Request $request, AuthRepositoryInterface $authRepository)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = $authRepository->findByEmail($request->email);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['message' => 'Login successful', 'access_token' => $token, 'token_type' => 'Bearer']);
    }

    /**
     * @OA\POST(
     *     path="/logout",
     *     summary="Logout a user",
     *     parameters={},
     *     tags={"Auth"},
     *     @OA\Response(response=200, description="Logout successful"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout successful']);
    }

    public function test() {
        return $this->success(null, 'You have accessed a protected route');
    }
}