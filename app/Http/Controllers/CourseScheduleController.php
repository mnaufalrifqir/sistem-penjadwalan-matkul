<?php

namespace App\Http\Controllers;

use App\Models\CourseSchedule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CourseScheduleController extends Controller
{
    /**
     * Display a listing of the course schedules with related course and lecturer data.
     */
    public function index()
    {

        $courseSchedules = CourseSchedule::with(['course', 'lecturer.user'])
            ->paginate(10);

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully retrieved all course schedules',
            'data' => $courseSchedules
        ], 200);
    }

    /**
     * Store a newly created course schedule in storage.
     */
    public function store()
    {
        $validatedData = Validator::make(request()->all(), [
            'room' => 'required|string|max:255',
            'day' => 'required|string',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'course_id' => [
                'required',
                Rule::exists('courses', 'id')->whereNull('deleted_at')
            ],
            'lecturer_id' => 'required|exists:lecturers,id',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'status' => 'Validation Error',
                'message' => $validatedData->errors()
            ], 400);
        }

        $courseSchedule = CourseSchedule::create([
            'room' => request('room'),
            'day' => request('day'),
            'start_time' => request('start_time'),
            'end_time' => request('end_time'),
            'course_id' => request('course_id'),
            'lecturer_id' => request('lecturer_id')
        ]);

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully created course schedule',
            'data' => $courseSchedule
        ], 201);
    }

    /**
     * Display the specified course schedule along with related course and lecturer data.
     */
    public function show($id)
    {
        $courseSchedule = CourseSchedule::with(['course', 'lecturer.user'])
            ->find($id);

        if (!$courseSchedule) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Course schedule not found'
            ], 404);
        }

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully retrieved course schedule',
            'data' => $courseSchedule
        ], 200);
    }

    /**
     * Update the specified course schedule in storage.
     */
    public function update($id)
    {
        $courseSchedule = CourseSchedule::find($id);

        if (!$courseSchedule) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Course schedule not found'
            ], 404);
        }

        $validatedData = Validator::make(request()->all(), [
            'room' => 'sometimes|string|max:255',
            'day' => 'sometimes|string',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'course_id' => [
                'sometimes',
                Rule::exists('courses', 'id')->whereNull('deleted_at')
            ],
            'lecturer_id' => 'sometimes|exists:lecturers,id',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'status' => 'Validation Error',
                'message' => $validatedData->errors()
            ], 400);
        }

        $courseSchedule->update([
            'room' => request('room', $courseSchedule->room),
            'day' => request('day', $courseSchedule->day),
            'start_time' => request('start_time', $courseSchedule->start_time),
            'end_time' => request('end_time', $courseSchedule->end_time),
            'course_id' => request('course_id', $courseSchedule->course_id),
            'lecturer_id' => request('lecturer_id', $courseSchedule->lecturer_id)
        ]);

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully updated course schedule',
            'data' => $courseSchedule
        ], 200);
    }

    /**
     * Remove the specified course schedule from storage.
     */
    public function destroy($id)
    {
        $courseSchedule = CourseSchedule::find($id);

        if (!$courseSchedule) {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Course schedule not found'
            ], 404);
        }

        $courseSchedule->delete();

        return response()->json([
            'status' => 'OK',
            'message' => 'Successfully deleted course schedule'
        ], 200);
    }
}