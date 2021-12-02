<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Tribe;
use App\Models\Course;
use App\Models\Facilitator;
use App\Observers\UserObserver;
use App\Observers\TribeObserver;
use Laravel\Telescope\Telescope;
use App\Observers\CourseObserver;
use App\Resolvers\SocialUserResolver;
use App\Observers\FacilitatorObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public $bindings = [
        SocialUserResolverInterface::class => SocialUserResolver::class,
    ];

    public function register()
    {


        Telescope::avatar(function ($id, $email) {
            if (auth('admin')->user()) {
                return '';
            }
            if (auth('facilitator')->user()) {
                return Facilitator::find($id)->profile;
            }
            if (auth('api')->user()) {
                return User::find($id)->profile;
            }
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        JsonResource::withoutWrapping();
        Tribe::observe(TribeObserver::class);
    }
}
