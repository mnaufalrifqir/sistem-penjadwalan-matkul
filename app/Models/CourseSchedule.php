<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room',
        'day',
        'start_time',
        'end_time',
        'course_id',
        'lecturer_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function studentSchedules()
    {
        return $this->hasMany(StudentSchedule::class);
    }
}