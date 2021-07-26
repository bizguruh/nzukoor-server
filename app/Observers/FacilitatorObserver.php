<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;
use App\Models\Facilitator;

class FacilitatorObserver
{
    /**
     * Handle the Facilitator "created" event.
     *
     * @param  \App\Models\Facilitator  $facilitator
     * @return void
     */
    public function created(Facilitator $facilitator)
    {
        Cache::forget('facilitator');
    }

    /**
     * Handle the Facilitator "updated" event.
     *
     * @param  \App\Models\Facilitator  $facilitator
     * @return void
     */
    public function updated(Facilitator $facilitator)
    {
        Cache::forget('facilitator');
    }

    /**
     * Handle the Facilitator "deleted" event.
     *
     * @param  \App\Models\Facilitator  $facilitator
     * @return void
     */
    public function deleted(Facilitator $facilitator)
    {
        Cache::forget('facilitator');
    }

    /**
     * Handle the Facilitator "restored" event.
     *
     * @param  \App\Models\Facilitator  $facilitator
     * @return void
     */
    public function restored(Facilitator $facilitator)
    {
        //
    }

    /**
     * Handle the Facilitator "force deleted" event.
     *
     * @param  \App\Models\Facilitator  $facilitator
     * @return void
     */
    public function forceDeleted(Facilitator $facilitator)
    {
        //
    }
}
