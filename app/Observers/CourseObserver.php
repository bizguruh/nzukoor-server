<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;
use App\Models\Course;

class CourseObserver
{
    /**
     * Handle the Course "created" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function created(Course $course)
    {
        Cache::forget('course');
    }

    /**
     * Handle the Course "updated" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function updated(Course $course)
    {
        Cache::forget('course');
    }

    /**
     * Handle the Course "deleted" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function deleted(Course $course)
    {
        Cache::forget('course');
    }

    /**
     * Handle the Course "restored" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function restored(Course $course)
    {
        //
    }

    /**
     * Handle the Course "force deleted" event.
     *
     * @param  \App\Models\Course  $course
     * @return void
     */
    public function forceDeleted(Course $course)
    {
        //
    }
}
