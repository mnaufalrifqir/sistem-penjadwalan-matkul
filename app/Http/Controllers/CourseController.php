<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::paginate(10);

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully retrieved all courses',
            'data' => $courses
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store()
    {
        $validatedData = Validator::make(request()->all(), [
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('courses')->whereNull('deleted_at')
            ],
            'credit' => 'required|integer|min:1',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'status' => 'Bad Request',
                'message' => 'Invalid input',
                'errors' => $validatedData->errors()
            ], 400);
        }

        $course = Course::create([
            'name' => request('name'),
            'code' => request('code'),
            'credit' => request('credit'),
        ]);

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully created course',
            'data' => $course
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Course not found',
            ], 404);
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully retrieved course',
            'data' => $course
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Course not found',
            ], 404);
        }

        $validatedData = Validator::make(request()->all(), [
            'name' => 'sometimes|string|max:255',
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('courses')->whereNull('deleted_at')
            ],
            'credit' => 'sometimes|integer|min:1',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'status' => 'Bad Request',
                'message' => 'Invalid input',
                'errors' => $validatedData->errors()
            ], 400);
        }

        $course->update([
            'name' => request('name', $course->name),
            'code' => request('code', $course->code),
            'credit' => request('credit', $course->credit),
        ]);

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully updated course',
            'data' => $course
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Course not found',
            ], 404);
        }

        $course->delete();

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully deleted course',
        ], 200);
    }
}