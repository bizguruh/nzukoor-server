<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Course;
use App\Models\Facilitator;
use App\Models\User;
use App\Observers\CourseObserver;
use App\Observers\FacilitatorObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Resolvers\SocialUserResolver;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeServiceProvider;

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
        User::observe(UserObserver::class);
        Course::observe(CourseObserver::class);
        Facilitator::observe(FacilitatorObserver::class);
    }
}
