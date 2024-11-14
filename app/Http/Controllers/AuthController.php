<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'Invalid credentials',
                'error' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully logged in',
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60
            ]
        ], 200);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register()
    {
        $validatedData = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:lecturer,student',
            'nim' => 'required_if:role,student|string|unique:students,nim|max:10',
            'major' => 'required_if:role,student|string|max:255',
            'class_year' => 'required_if:role,student|string|max:4',
            'nip' => 'required_if:role,lecturer|string|unique:lecturers,nip|max:8',
            'code' => 'required_if:role,lecturer|string|max:3|unique:lecturers,code',
        ]);
    
        if ($validatedData->fails()) {
            return response()->json([
                'status' => 'Bad Request',
                'message' => 'The given data was invalid.',
                'error' => $validatedData->errors()
            ], 400);
        }
    
        DB::beginTransaction();
        
        try {
            $user = User::create([
                'name' => request('name'),
                'email' => request('email'),
                'password' => Hash::make(request('password')),
                'role' => request('role'),
            ]);
    
            if ($user->role === 'student') {
                $user->student()->create([
                    'nim' => request('nim'),
                    'major' => request('major'),
                    'class_year' => request('class_year'),
                ]);
            } elseif ($user->role === 'lecturer') {
                $user->lecturer()->create([
                    'nip' => request('nip'),
                    'code' => request('code'),
                ]);
            }
    
            DB::commit();

            $token = auth('api')->attempt(['email' => request('email'), 'password' => request('password')]);
    
            return response()->json([
                'status' => 'Created',
                'message' => 'User successfully registered',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60
                ]
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'Internal Server Error',
                'message' => 'Failed to register user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json([
            'status' => 'OK',
            'message' => 'User data successfully retrieved',
            'data' => auth('api')->user()
        ], 200);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully logged out'
        ], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return response()->json([
            'status' => 'OK',
            'message' => 'Token successfully refreshed',
            'data' => [
                'access_token' => JWTAuth::parseToken()->refresh(),
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60
            ]
        ], 200);
    }
}