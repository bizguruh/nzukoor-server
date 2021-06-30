<?php

use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\FacilitatorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnsweredQuestionnaireController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AssessmentResponseController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\CourseCommunityController;
use App\Http\Controllers\CourseCommunityLinkController;
use App\Http\Controllers\ContributorController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FeedCommentController;
use App\Http\Controllers\FeedLikeController;
use App\Http\Controllers\FeedStarController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseOutlineController;
use App\Http\Controllers\CourseScheduleController;
use App\Http\Controllers\CourseViewCountController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\DiscussionMessageController;
use App\Http\Controllers\DiscussionRequestController;
use App\Http\Controllers\DiscussionVoteController;
use App\Http\Controllers\DiscussionViewController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FacilitatorModuleController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\LearnerAssessmentController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\LoginHistoryController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PrivateDiscussionMemberController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\QuestionResponseController;
use App\Http\Controllers\QuestionTemplateController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TodoController;
use App\Models\DiscussionRequest;
use App\Models\PrivateDiscussionMember;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
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
    $login = new LoginHistoryController;
    $login->store();
    return $request->user()->load('organization');
});
Route::post('admin', [AdminController::class, 'store']);

Route::middleware(['auth:admin'])->group(function () {
    Route::apiResource('admins', AdminController::class);

    // Route::get('admin-get-facilitator/{id}', [OrganizationController::class, 'getfacilitator']);
    Route::put('admin-update-facilitator/{id}', [OrganizationController::class, 'updatefacilitator']);
    Route::delete('admin-delete-facilitator/{id}', [OrganizationController::class, 'deletefacilitator']);

    Route::get('admin-get-users', [OrganizationController::class, 'admingetusers']);
    Route::get('admin-get-user/{id}', [OrganizationController::class, 'getuser']);
    Route::put('admin-update-user/{id}', [OrganizationController::class, 'updateuser']);
    Route::delete('admin-delete-user/{id}', [OrganizationController::class, 'deleteuser']);


    Route::get('admin-get-facilitators', [FacilitatorController::class, 'admingetfacilitators']);
    Route::get('admin-get-facilitator/{id}', [FacilitatorController::class, 'admingetfacilitator']);

    Route::apiResource('curriculums', CurriculumController::class);
});
// admin api routes ends here


// facilitator api routes begin here

Route::middleware('auth:facilitator')->get('/facilitator', function (Request $request) {
    $login = new LoginHistoryController;
    $login->store();
    return $request->user()->load('organization');
});

Route::post('facilitator-register', [FacilitatorController::class, 'storefacilitator']);

Route::post('facilitator-register-user', [UserController::class, 'facilitatorStoreUser']);


Route::middleware(['auth:facilitator'])->group(function () {



    Route::apiResource('facilitators', FacilitatorController::class);

    Route::apiResource('facilitator/modules', FacilitatorModuleController::class);

    Route::get('facilitator/get-user/{id}', [OrganizationController::class, 'getuser']);
    Route::put('facilitator/update-user/{id}', [OrganizationController::class, 'updateuser']);
    Route::post('facilitator/delete-user/{id}', [OrganizationController::class, 'deleteuser']);


    Route::get('facilitator-get-events', [EventController::class, 'facilitatorgetevents']);
    Route::get('facilitator-get-event/{id}', [EventController::class, 'facilitatorgetevent']);

    Route::get('facilitator-get-users', [UserController::class, 'facilitatorgetusers']);
    Route::get('facilitator-get-user/{id}', [UserController::class, 'facilitatorgetuser']);

    Route::get('facilitator-get-admins', [AdminController::class, 'facilitatorgetadmins']);
    Route::get('facilitator-get-admin/{id}', [AdminController::class, 'facilitatorgetadmin']);
});

// facilitator api routes ends here



// User api routes begin here

Route::middleware('auth:api')->get('/user', function (Request $request) {
    $login = new LoginHistoryController;
    $login->store();
    return $request->user()->load('organization');
});


Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('users', UserController::class);


    Route::apiResource('feedbacks', FeedbackController::class);



    Route::get('user-get-events', [EventController::class, 'usergetevents']);
    Route::get('user-get-event/{id}', [EventController::class, 'usergetevent']);

    Route::get('user-get-facilitators', [FacilitatorController::class, 'usergetfacilitators']);
    Route::get('user-get-facilitator/{id}', [FacilitatorController::class, 'usergetfacilitator']);
});

Route::post('user-register', [UserController::class, 'storeuser']);

// User api routes ends here


// Social auths
Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect']);
Route::post('/auth/{provider}/callback', [SocialLoginController::class, 'callback']);


//Inbox routes

Route::apiResource('inboxes', InboxController::class);


//Todos routes

Route::apiResource('todos', TodoController::class);
Route::get('todos-destroy', [TodoController::class, 'destroyall']);

// discussions route

Route::apiResource('discussions', DiscussionController::class);
Route::apiResource('discussion-messages', DiscussionMessageController::class);

Route::apiResource('views', DiscussionViewController::class);
Route::apiResource('votes', DiscussionVoteController::class);
Route::get('add-view/{id}', [DiscussionViewController::class, 'addview']);

Route::apiResource('votes', DiscussionVoteController::class);
Route::apiResource('tags', TagController::class);
Route::apiResource('feeds', FeedController::class);
Route::apiResource('feed-comments', FeedCommentController::class);
Route::apiResource('feed-likes', FeedLikeController::class);
Route::apiResource('feed-stars', FeedStarController::class);
Route::apiResource('login-history', LoginHistoryController::class);

Route::apiResource('courses', CourseController::class);

Route::post('update/progress', [LibraryController::class, 'updateprogress']);


Route::apiResource('courseschedules', CourseScheduleController::class);

Route::apiResource('courseoutlines', CourseOutlineController::class);

Route::apiResource('events', EventController::class);

Route::apiResource('modules', ModuleController::class);


Route::post('send-notification', [NotificationController::class, 'sendnotification']);
Route::post('send-notifications', [NotificationController::class, 'sendnotifications']);
Route::get('get-notifications', [NotificationController::class, 'getnotifications']);
Route::get('mark-notifications', [NotificationController::class, 'markreadnotifications']);
Route::get('mark-notification/{id}', [NotificationController::class, 'marksinglenotification']);
Route::get('unread-notifications', [NotificationController::class, 'unreadnotifications']);
Route::get('read-notifications', [NotificationController::class, 'readnotifications']);

Route::apiResource('connections', ConnectionController::class);

Route::apiResource('libraries', LibraryController::class);
Route::apiResource('answer-questionnaires', AnsweredQuestionnaireController::class);

Route::get('edit/response/{id}', [AnsweredQuestionnaireController::class, 'editresponse']);

Route::apiResource('question/responses', QuestionResponseController::class);

Route::apiResource('assessments', AssessmentController::class);

Route::apiResource('assessment/responses', AssessmentResponseController::class);
Route::apiResource('learner/assessments', LearnerAssessmentController::class);
Route::get('add/assessment', [LearnerAssessmentController::class, 'addassessment']);

Route::apiResource('orders', OrderController::class);
Route::get('course/view/{id}', [CourseViewCountController::class, 'store']);




Route::apiResource('questionnaires', QuestionnaireController::class);
Route::apiResource('question/templates', QuestionTemplateController::class);
// Question drafts
Route::get('question/drafts', [QuestionTemplateController::class, 'indexdraft']);
Route::put('question/draft/{id}',  [QuestionTemplateController::class, 'updatedraft']);
Route::post('question/drafts', [QuestionTemplateController::class, 'storedraft']);
Route::delete('question/draft/{id}', [QuestionTemplateController::class, 'destroydraft']);
Route::put('question/draft/update/{id}', [QuestionTemplateController::class, 'makeactive']);


Route::post('delete-connection/{id}', [ConnectionController::class, 'deleteconnection']);

Route::get('get-course/{id}', [CourseController::class, 'getcourse']);


//Update password
Route::post('update-password', [UserController::class, 'updatepassword']);

Route::post('reset-password', [UserController::class, 'resetpassword']);

//update interests
Route::post('save-interests', [UserController::class, 'saveinterests']);


Route::get('identical-learners', [ConnectionController::class, 'getlearnerswithinterests']);
Route::get('identical-facilitators', [ConnectionController::class, 'getfacilitatorswithinterests']);


Route::get('other-discussions', [ConnectionController::class, 'getidenticaldiscusiions']);
Route::get('interest-courses', [ConnectionController::class, 'getidenticalcourses']);


// Mail routes
Route::get('send-mail', [MailController::class, 'sendwelcome']);
Route::post('send-referral', [MailController::class, 'sendreferral']);

Route::post('send/invite', [MailController::class, 'sendcourseinvite']);
Route::post('send/discussion/invite', [MailController::class, 'senddiscussioninvite']);
Route::post('send/event/invite', [MailController::class, 'sendeventinvite']);



//Referral routes

Route::apiResource('referrals', ReferralController::class);


// Notification request

Route::post('join-discussion', [NotificationController::class, 'joinDiscussionRequest']);

// Apply course
Route::apiResource('apply-community', CourseCommunityLinkController::class);

Route::apiResource('add-community', CourseCommunityController::class);


Route::apiResource('reviews', ReviewController::class);
Route::get('mostenrolled', [CourseController::class, 'mostenrolled']);
Route::get('toprated', [CourseController::class, 'toprated']);


// Guest
Route::get('guest/mostenrolled', [CourseController::class, 'guestmostenrolled']);
Route::get('guest/learners', [UserController::class, 'index']);
Route::get('guest/facilitators', [FacilitatorController::class, 'index']);
Route::get('guest/courses', [CourseController::class, 'guestcourses']);
Route::get('guest/discussions', [DiscussionController::class, 'guestdiscussions']);
Route::get('guest/events', [EventController::class, 'index']);
Route::get('guest/feeds', [FeedController::class, 'guestfeeds']);


// Contact message
Route::post('send/message', [MailController::class, 'contactmail']);

Route::apiResource('contributors', ContributorController::class);


Route::apiResource('discussion/private', PrivateDiscussionMemberController::class);

Route::apiResource('discussion/requests', DiscussionRequestController::class);

Route::post('discussion/reject', [NotificationController::class, 'discussionreject']);
