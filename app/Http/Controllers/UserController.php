<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth('api')->user()->role !== 'admin') {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'You are not authorized to access this resource',
            ], 401);
        }

        $users = User::with([
            'student',
            'lecturer'
        ])->paginate(10);

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully retrieved all users',
            'data' => $users
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if (auth('api')->user()->role !== 'admin') {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'You are not authorized to access this resource',
            ], 401);
        }
        
        $user = User::with([
            'student',
            'lecturer'
        ])->find($id);
        
        if (!$user) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'User not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully retrieved user',
            'data' => $user
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'User not found',
                'data' => null
            ], 404);
        }

        $validatedData = Validator::make(request()->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|unique:users|max:255',
            'password' => 'sometimes|string|min:8',
            'nim' => 'sometimes|string|unique:students,nim|max:10',
            'major' => 'sometimes|string|max:255',
            'class_year' => 'sometimes|string|max:4',
            'nip' => 'sometimes|string|unique:lecturers,nip|max:8',
            'code' => 'sometimes|string|max:3|unique:lecturers,code',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'status' => 'Unprocessable Entity',
                'message' => 'Invalid input',
                'errors' => $validatedData->errors()
            ], 422);
        }
        
        if (request('password')) {
            $user->password = Hash::make(request('password'));
        }

        $user->update([
            'name' => request('name', $user->name),
            'email' => request('email', $user->email),
            'password' => $user->password,
        ]);

        if ($user->role == 'student') {
            $user->student()->update([
                'nim' => request('nim', $user->student->nim),
                'major' => request('major', $user->student->major),
                'class_year' => request('class_year', $user->student->class_year)
            ]);
        }

        if ($user->role == 'lecturer') {
            $user->lecturer()->update([
                'nip' => request('nip', $user->lecturer->nip),
                'code' => request('code', $user->lecturer->code)
            ]);
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully updated user',
            'data' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (auth('api')->user()->role !== 'admin') {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'You are not authorized to access this resource',
            ], 401);
        }
        
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'User not found',
                'data' => null
            ], 404);
        }

        $user->delete();

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully deleted user',
        ], 200);
    }

    /**
     * Get all users with student role and their data.
     */
    public function getAllStudents()
    {
        if (auth('api')->user()->role !== 'admin') {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'You are not authorized to access this resource',
            ], 401);
        }
        
        $students = User::with([
            'student'
        ])->where('role', 'student')->paginate(10);
        
        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully retrieved user',
            'data' => $students
        ], 200);
    }

    /**
     * Get all users with lecturer role and their data.
     */
    public function getAllLecturers()
    {
        if (auth('api')->user()->role !== 'admin') {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'You are not authorized to access this resource',
            ], 401);
        }
        
        $lecturers = User::with([
            'lecturer'
        ])->where('role', 'lecturer')->paginate(10);
        
        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully retrieved all lecturers',
            'data' => $lecturers
        ], 200);
    }
}