<?php

use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\FacilitatorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\DiscussionMessageController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\TodoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
header('Access-Control-Allow-Origin: *');

// organization api routes begin here
Route::middleware('auth:organization')->get('organization', function (Request $request) {
    return $request->user();
});

Route::post('register-organization', [OrganizationController::class, 'store']);


Route::middleware(['auth:organization'])->group(function () {


    Route::apiResource('organizations', OrganizationController::class);


    Route::post('register-admin', [AdminController::class, 'store']);
    Route::get('get-admins', [OrganizationController::class, 'index']);
    Route::get('get-admin/{id}', [OrganizationController::class, 'getadmin']);
    Route::put('update-admin/{id}', [OrganizationController::class, 'updateadmin']);
    Route::delete('delete-admin/{id}', [OrganizationController::class, 'deleteadmin']);


    Route::post('register-facilitator', [FacilitatorController::class, 'store']);
    Route::get('get-facilitators', [FacilitatorController::class, 'getfacilitators']);
    Route::get('get-facilitator/{id}', [OrganizationController::class, 'getfacilitator']);
    Route::put('update-facilitator/{id}', [OrganizationController::class, 'updatefacilitator']);
    Route::delete('delete-facilitator/{id}', [OrganizationController::class, 'deletefacilitator']);

    Route::post('register-user', [UserController::class, 'store']);
    Route::get('get-users', [OrganizationController::class, 'getusers']);
    Route::get('get-user/{id}', [OrganizationController::class, 'getuser']);
    Route::put('update-user/{id}', [OrganizationController::class, 'updateuser']);
    Route::delete('delete-user/{id}', [OrganizationController::class, 'deleteuser']);
});
// organizations api routes ends here


// admin api routes begin here
Route::middleware('auth:admin')->get('/admin', function (Request $request) {
    return $request->user();
});
Route::post('admin', [AdminController::class, 'store']);

Route::middleware(['auth:admin'])->group(function () {
    Route::apiResource('admins', AdminController::class);

    // Route::get('admin-get-facilitator/{id}', [OrganizationController::class, 'getfacilitator']);
    Route::put('admin-update-facilitator/{id}', [OrganizationController::class, 'updatefacilitator']);
    Route::post('admin-delete-facilitator/{id}', [OrganizationController::class, 'deletefacilitator']);

    Route::get('admin-get-users', [OrganizationController::class, 'admingetusers']);
    Route::get('admin-get-user/{id}', [OrganizationController::class, 'getuser']);
    Route::put('admin-update-user/{id}', [OrganizationController::class, 'updateuser']);
    Route::post('admin-delete-user/{id}', [OrganizationController::class, 'deleteuser']);


    Route::get('admin-get-facilitators', [FacilitatorController::class, 'admingetfacilitators']);
    Route::get('admin-get-facilitator/{id}', [FacilitatorController::class, 'admingetfacilitator']);

    Route::apiResource('events', EventController::class);

    Route::apiResource('courses', CourseController::class);

    Route::apiResource('curriculums', CurriculumController::class);
});
// admin api routes ends here


// facilitator api routes begin here

Route::middleware('auth:facilitator')->get('/facilitator', function (Request $request) {
    return $request->user();
});

Route::post('facilitator-register', [FacilitatorController::class, 'storefacilitator']);


Route::middleware(['auth:facilitator'])->group(function () {

    Route::apiResource('modules', ModuleController::class);

    Route::apiResource('facilitators', FacilitatorController::class);

    Route::get('facilitator/get-user/{id}', [OrganizationController::class, 'getuser']);
    Route::put('facilitator/update-user/{id}', [OrganizationController::class, 'updateuser']);
    Route::post('facilitator/delete-user/{id}', [OrganizationController::class, 'deleteuser']);

    Route::get('facilitator-get-courses', [CourseController::class, 'facilitatorgetcourses']);
    Route::get('facilitator-get-course/{id}', [CourseController::class, 'facilitatorgetcourse']);

    Route::get('facilitator-get-events', [EventController::class, 'facilitatorgetevents']);
    Route::get('facilitator-get-event/{id}', [EventController::class, 'facilitatorgetevent']);

    Route::get('facilitator-get-users', [UserController::class, 'facilitatorgetusers']);
    Route::get('facilitator-get-user/{id}', [UserController::class, 'facilitatorgetuser']);


    Route::post('facilitator-add-discussion', [DiscussionController::class, 'store']);
    Route::get('facilitator-get-discussion/{id}', [DiscussionController::class, 'getdiscussion']);
    Route::get('facilitator-get-discussions', [DiscussionController::class, 'index']);

    Route::post('facilitator-add-message', [DiscussionMessageController::class, 'store']);
});

// facilitator api routes ends here



// User api routes begin here

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('users', UserController::class);


    Route::apiResource('feedbacks', FeedbackController::class);


    Route::get('user-get-courses', [CourseController::class, 'usergetcourses']);
    Route::get('user-get-course/{id}', [CourseController::class, 'usergetcourse']);

    Route::get('user-get-events', [EventController::class, 'usergetevents']);
    Route::get('user-get-event/{id}', [EventController::class, 'usergetevent']);

    Route::get('user-get-facilitator', [FacilitatorController::class, 'usergetfacilitators']);
    Route::get('user-get-facilitator/{id}', [FacilitatorController::class, 'usergetfacilitator']);

    Route::post('user-add-discussion', [DiscussionController::class, 'store']);
    Route::get('user-get-discussion/{id}', [DiscussionController::class, 'getdiscussion']);
    Route::get('user-get-discussions', [DiscussionController::class, 'index']);

    Route::post('add-discussion-message', [DiscussionMessageController::class, 'store']);
});

Route::post('register-user', [UserController::class, 'storeuser']);

// User api routes ends here


// Social auths
Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback']);


//Inbox routes

Route::post('inbox', [InboxController::class, 'store']);
Route::get('inbox', [InboxController::class, 'getInbox']);
Route::delete('inbox/{id}', [InboxController::class, 'destroy']);


//Todos routes

Route::apiResource('todos', TodoController::class);
Route::get('todos-destroy', [TodoController::class, 'destroyall']);
