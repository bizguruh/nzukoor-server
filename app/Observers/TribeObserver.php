<?php

namespace App\Observers;

use App\Models\Tribe;
use Illuminate\Support\Facades\Cache;

class TribeObserver
{
    /**
     * Handle the Tribe "created" event.
     *
     * @param  \App\Models\Tribe  $tribe
     * @return void
     */
    public function created(Tribe $tribe)
    {
        Cache::tags('tribes')->flush();
        Cache::tags('showtribe')->flush();
        Cache::tags('guesttribes')->flush();
        Cache::tags('tribemembers')->flush();
        Cache::tags('tribediscussions')->flush();
        Cache::tags('usertribes')->flush();
        Cache::tags('showtribe')->flush();
    }

    /**
     * Handle the Tribe "updated" event.
     *
     * @param  \App\Models\Tribe  $tribe
     * @return void
     */
    public function updated(Tribe $tribe)
    {
        Cache::tags('tribes')->flush();
        Cache::tags('showtribe')->flush();
        Cache::tags('guesttribes')->flush();
        Cache::tags('tribemembers')->flush();
        Cache::tags('tribediscussions')->flush();
        Cache::tags('usertribes')->flush();
        Cache::tags('showtribe')->flush();
    }

    /**
     * Handle the Tribe "deleted" event.
     *
     * @param  \App\Models\Tribe  $tribe
     * @return void
     */
    public function deleted(Tribe $tribe)
    {
        Cache::tags('tribes')->flush();
        Cache::tags('showtribe')->flush();
        Cache::tags('guesttribes')->flush();
        Cache::tags('tribemembers')->flush();
        Cache::tags('tribediscussions')->flush();
        Cache::tags('usertribes')->flush();
        Cache::tags('showtribe')->flush();
    }

    /**
     * Handle the Tribe "restored" event.
     *
     * @param  \App\Models\Tribe  $tribe
     * @return void
     */
    public function restored(Tribe $tribe)
    {
        Cache::tags('tribes')->flush();
        Cache::tags('showtribe')->flush();
        Cache::tags('guesttribes')->flush();
        Cache::tags('tribemembers')->flush();
        Cache::tags('tribediscussions')->flush();
        Cache::tags('usertribes')->flush();
        Cache::tags('showtribe')->flush();
    }

    /**
     * Handle the Tribe "force deleted" event.
     *
     * @param  \App\Models\Tribe  $tribe
     * @return void
     */
    public function forceDeleted(Tribe $tribe)
    {
        Cache::tags('tribes')->flush();
        Cache::tags('showtribe')->flush();
        Cache::tags('guesttribes')->flush();
        Cache::tags('tribemembers')->flush();
        Cache::tags('tribediscussions')->flush();
        Cache::tags('usertribes')->flush();
        Cache::tags('showtribe')->flush();
    }
}
