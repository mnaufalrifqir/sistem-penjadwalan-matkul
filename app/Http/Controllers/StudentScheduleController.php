<?php

namespace App\Http\Controllers;

use App\Models\StudentSchedule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StudentScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    /**
     * Display a listing of student schedules with related course schedule and student data.
     */
    public function index()
        {
            if (auth('api')->user()->role !== 'student' && auth('api')->user()->role !== 'admin') {
                return response()->json([
                    'status' => 'Unauthorized',
                    'message' => 'You are not authorized to access this resource',
                ], 401);
            }

            $studentSchedules = StudentSchedule::with([
                'courseSchedule.course',
                'courseSchedule.lecturer.user',
            ])->where('student_id', auth('api')->user()->student->id)
                ->paginate(10);
            
            return response()->json([
                'status' => 'OK',
                'message' => 'Successfully retrieved all student schedules',
                'data' => $studentSchedules
            ], 200);
        }


    /**
     * Store a newly created student schedule in storage.
     */
    public function store()
    {
        if (auth('api')->user()->role !== 'student' && auth('api')->user()->role !== 'admin') {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'You are not authorized to access this resource',
            ], 401);
        }
        
        $validatedData = Validator::make(request()->all(), [
            'course_schedule_id' => [
                'required',
                Rule::exists('course_schedules', 'id')->whereNull('deleted_at')
            ],
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'status' => 'Unprocessable Entity',
                'message' => $validatedData->errors()
            ], 422);
        }

        $studentSchedule = StudentSchedule::create([
            'student_id' => request('student_id', auth('api')->user()->student->id),
            'course_schedule_id' => request('course_schedule_id')
        ]);

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully created student schedule',
            'data' => $studentSchedule
        ], 201);
    }

    /**
     * Display the specified student schedule with related course schedule and student data.
     */
    public function show($id)
    {
        if (auth('api')->user()->role !== 'student' && auth('api')->user()->role !== 'admin') {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'You are not authorized to access this resource',
            ], 401);
        }
        
        $studentSchedule = StudentSchedule::with([
            'courseSchedule.course',
            'courseSchedule.lecturer.user',
            'student.user'
        ])->find($id);

        if (!$studentSchedule) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Student schedule not found'
            ], 404);
        }

        if (auth('api')->user()->role === 'student' && auth('api')->user()->student->id !== $studentSchedule->student_id) {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'You are not authorized to access this resource',
            ], 401);
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully retrieved student schedule',
            'data' => $studentSchedule
        ], 200);
    }

    /**
     * Update the specified student schedule in storage.
     */
    public function update($id)
    {
        if (auth('api')->user()->role !== 'student' && auth('api')->user()->role !== 'admin') {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'You are not authorized to access this resource',
            ], 401);
        }
        
        $studentSchedule = StudentSchedule::find($id);

        if (!$studentSchedule) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Student schedule not found'
            ], 404);
        }

        if (auth('api')->user()->role === 'student' && auth('api')->user()->student->id !== $studentSchedule->student_id) {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'You are not authorized to access this resource',
            ], 401);
        }

        $validatedData = Validator::make(request()->all(), [
            'student_id' => 'sometimes|exists:students,id',
            'course_schedule_id' => [
                'sometimes',
                Rule::exists('course_schedules', 'id')->whereNull('deleted_at')
            ],
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'status' => 'Unprocessable Entity',
                'message' => $validatedData->errors()
            ], 422);
        }

        if (request('student_id') && (auth('api')->user()->role === 'student' && auth('api')->user()->student->id !== request('student_id'))) {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'You are not authorized to access this resource',
            ], 401);
        }

        $studentSchedule->update([
            'student_id' => request('student_id', $studentSchedule->student_id),
            'course_schedule_id' => request('course_schedule_id', $studentSchedule->course_schedule_id)
        ]);

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully updated student schedule',
            'data' => $studentSchedule
        ], 200);
    }

    /**
     * Remove the specified student schedule from storage.
     */
    public function destroy($id)
    {
        if (auth('api')->user()->role !== 'student' && auth('api')->user()->role !== 'admin') {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'You are not authorized to access this resource',
            ], 401);
        }

        $studentSchedule = StudentSchedule::find($id);

        if (!$studentSchedule) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Student schedule not found'
            ], 404);
        }

        if (auth('api')->user()->role === 'student' && auth('api')->user()->student->id !== $studentSchedule->student_id) {
            return response()->json([
                'status' => 'Unauthorized',
                'message' => 'You are not authorized to access this resource',
            ], 401);
        }

        $studentSchedule->delete();

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully deleted student schedule'
        ], 200);
    }
}