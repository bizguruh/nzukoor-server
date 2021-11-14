<?php

use Illuminate\Http\Request;
use App\Models\DiscussionRequest;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;
use App\Models\PrivateDiscussionMember;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TribeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReviewController;
use Illuminate\Notifications\Notification;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FeedLikeController;
use App\Http\Controllers\FeedStarController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\BankDetailController;
use App\Http\Controllers\ConnectionController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\ContributorController;
use App\Http\Controllers\FacilitatorController;
use App\Http\Controllers\FeedCommentController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\TribeSearchController;
use App\Http\Controllers\LoginHistoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\CourseOutlineController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\CourseScheduleController;
use App\Http\Controllers\DiscussionViewController;
use App\Http\Controllers\DiscussionVoteController;
use App\Http\Controllers\CourseCommunityController;
use App\Http\Controllers\CourseViewCountController;
use App\Http\Controllers\EventAttendanceController;
use App\Http\Controllers\UserInformationController;
use App\Http\Controllers\FeedCommentReplyController;
use App\Http\Controllers\MemberAssessmentController;
use App\Http\Controllers\OrganizationFeedController;
use App\Http\Controllers\QuestionResponseController;
use App\Http\Controllers\QuestionTemplateController;
use App\Http\Controllers\DiscussionMessageController;
use App\Http\Controllers\DiscussionRequestController;
use App\Http\Controllers\FacilitatorModuleController;
use App\Http\Controllers\OrganizationEventController;
use App\Http\Controllers\OrganizationTribeController;
use App\Http\Controllers\AssessmentResponseController;
use App\Http\Controllers\OrganizationReportController;
use App\Http\Controllers\CourseCommunityLinkController;
use App\Http\Controllers\HighestEarningCourseController;
use App\Http\Controllers\AnsweredQuestionnaireController;
use App\Http\Controllers\OrganizationDiscussionController;
use App\Http\Controllers\PrivateDiscussionMemberController;
use App\Http\Controllers\DiscussionMessageCommentController;

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


    Route::post('register/admin', [OrganizationController::class, 'store']);
    Route::post('register/superadmin', [OrganizationController::class, 'storesuperadmin']);
    Route::get('get/organization/admins', [OrganizationController::class, 'index']);
    Route::get('get-admin/{id}', [OrganizationController::class, 'getadmin']);
    Route::put('update-admin/{id}', [OrganizationController::class, 'updateadmin']);
    Route::delete('delete-admin/{id}', [OrganizationController::class, 'deleteadmin']);
    Route::put('verify/admin/{id}', [OrganizationController::class, 'verifyadmin']);

    Route::put('verify/user/{id}', [OrganizationController::class, 'verifyuser']);


    Route::post('register-facilitator', [FacilitatorController::class, 'store']);
    Route::get('get-facilitators', [FacilitatorController::class, 'getfacilitators']);
    Route::get('get-facilitator/{id}', [OrganizationController::class, 'getfacilitator']);
    Route::put('update-facilitator/{id}', [OrganizationController::class, 'updatefacilitator']);
    Route::delete('delete-facilitator/{id}', [OrganizationController::class, 'deletefacilitator']);

    Route::post('register-user', [UserController::class, 'store']);
    Route::get('get/organization/users', [OrganizationController::class, 'getusers']);
    Route::get('get-user/{id}', [OrganizationController::class, 'getuser']);
    Route::put('update-user/{id}', [OrganizationController::class, 'updateuser']);
    Route::delete('delete-user/{id}', [OrganizationController::class, 'deleteuser']);



    Route::apiResource('get/organization/tribes', OrganizationTribeController::class);
    Route::apiResource('get/organization/discussions', OrganizationDiscussionController::class);
    Route::apiResource('get/organization/feeds', OrganizationFeedController::class);
    Route::apiResource('get/organization/reports', OrganizationReportController::class);
    Route::apiResource('get/organization/events', OrganizationEventController::class);
});
// organizations api routes ends here


// admin api routes begin here
// Route::middleware('auth:admin')->get('/admin', function (Request $request) {
//     $login = new LoginHistoryController;
//     $login->store();
//     return $request->user()->load('organization');
// });
// Route::post('admin', [AdminController::class, 'store']);

// Route::middleware(['auth:admin'])->group(function () {
//     Route::apiResource('admins', AdminController::class);

//     // Route::get('admin-get-facilitator/{id}', [OrganizationController::class, 'getfacilitator']);

//     Route::put('admin-update-facilitator/{id}', [OrganizationController::class, 'updatefacilitator']);
//     Route::delete('admin-delete-facilitator/{id}', [OrganizationController::class, 'deletefacilitator']);

//     Route::post('admin-register-user', [UserController::class, 'adminStoreUser']);

//     Route::get('admin-get-users', [OrganizationController::class, 'admingetusers']);
//     Route::get('admin-get-user/{id}', [OrganizationController::class, 'getuser']);
//     Route::put('admin-update-user/{id}', [OrganizationController::class, 'updateuser']);
//     Route::delete('admin-delete-user/{id}', [OrganizationController::class, 'deleteuser']);


//     Route::get('admin-get-facilitators', [FacilitatorController::class, 'admingetfacilitators']);
//     Route::get('admin-get-facilitator/{id}', [FacilitatorController::class, 'admingetfacilitator']);

//     Route::apiResource('curriculums', CurriculumController::class);
// });
// admin api routes ends here


// facilitator api routes begin here

// Route::middleware('auth:facilitator')->get('/facilitator', function (Request $request) {
//     $login = new LoginHistoryController;
//     $login->store();
//     return $request->user()->load('organization');
// });

// Route::post('facilitator-register', [FacilitatorController::class, 'storefacilitator']);

// Route::post('facilitator-register-user', [UserController::class, 'facilitatorStoreUser']);


// Route::middleware(['auth:facilitator'])->group(function () {



//     Route::apiResource('facilitators', FacilitatorController::class);

//     Route::apiResource('facilitator/modules', FacilitatorModuleController::class);

//     Route::get('facilitator/get-user/{id}', [OrganizationController::class, 'getuser']);
//     Route::put('facilitator/update-user/{id}', [OrganizationController::class, 'updateuser']);
//     Route::post('facilitator/delete-user/{id}', [OrganizationController::class, 'deleteuser']);


//     Route::get('facilitator-get-events', [EventController::class, 'facilitatorgetevents']);
//     Route::get('facilitator-get-event/{id}', [EventController::class, 'facilitatorgetevent']);

//     Route::get('facilitator-get-users', [UserController::class, 'facilitatorgetusers']);
//     Route::get('facilitator-get-user/{id}', [UserController::class, 'facilitatorgetuser']);

//     Route::get('facilitator-get-admins', [AdminController::class, 'facilitatorgetadmins']);
//     Route::get('facilitator-get-admin/{id}', [AdminController::class, 'facilitatorgetadmin']);
// });

// facilitator api routes ends here



// User api routes begin here

Route::middleware('auth:api')->get('/user', function (Request $request) {
    $login = new LoginHistoryController;
    $login->store();
    return $request->user()->load('organization', 'role');
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

Route::get('get/userprofile/{username}', [UserController::class, 'getuserbyusername']);

// User api routes ends here


// Social auths
Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect']);
Route::post('/auth/{provider}/callback', [SocialLoginController::class, 'callback']);


//Inbox routes

Route::apiResource('inboxes', InboxController::class);

Route::post('inboxes/mark/read', [InboxController::class, 'markread']);


//Todos routes

Route::apiResource('todos', TodoController::class);
Route::get('todos-destroy', [TodoController::class, 'destroyall']);

// discussions route

Route::apiResource('discussions', DiscussionController::class);
Route::get('custom/discussions', [DiscussionController::class, 'customdiscussions']);
Route::get('trending/discussions', [DiscussionController::class, 'trenddiscussions']);
Route::get('interest/discussions', [DiscussionController::class, 'interestdiscussions']);

Route::apiResource('discussion-messages', DiscussionMessageController::class);
Route::get('get/discussion/members/{id}', [DiscussionController::class, 'discussionmembers']);
Route::apiResource('discussion/message/replies', DiscussionMessageCommentController::class);

Route::apiResource('views', DiscussionViewController::class);
Route::apiResource('votes', DiscussionVoteController::class);
Route::get('add-view/{id}', [DiscussionViewController::class, 'addview']);
Route::apiResource('tags', TagController::class);
Route::apiResource('feeds', FeedController::class);
Route::apiResource('reports', ReportController::class);
Route::apiResource('feed/comment/reply', FeedCommentReplyController::class);
Route::post('feed/comment/reply/like', [FeedCommentReplyController::class, 'replylike']);
Route::get('trending/feeds', [FeedController::class, 'trendingFeedsByComments']);
Route::get('custom/feeds', [FeedController::class, 'customFeeds']);
Route::get('recent/feeds', [FeedController::class, 'recentFeedsByConnection']);


Route::apiResource('feed-comments', FeedCommentController::class);
Route::get('feed/likes/{id}', [FeedController::class, 'feedlikes']);
Route::get('feed/comments/{id}', [FeedController::class, 'feedcomments']);
Route::get('feed/comment/replies/{id}', [FeedCommentController::class, 'feedcommentreplies']);
Route::post('feed/comment/like', [FeedCommentController::class, 'commentlike']);
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

Route::get('my/connections', [ConnectionController::class, 'myconnections']);

Route::apiResource('libraries', LibraryController::class);
Route::apiResource('answer-questionnaires', AnsweredQuestionnaireController::class);

Route::get('edit/response/{id}', [AnsweredQuestionnaireController::class, 'editresponse']);

Route::apiResource('question/responses', QuestionResponseController::class);

Route::apiResource('assessments', AssessmentController::class);

Route::apiResource('revenue', RevenueController::class);

Route::get('facilitator/revenue', [RevenueController::class, 'facilitatorIndex']);

Route::apiResource('assessment/responses', AssessmentResponseController::class);
Route::apiResource('member/assessments', MemberAssessmentController::class);
Route::get('add/assessment', [MemberAssessmentController::class, 'addassessment']);

Route::apiResource('orders', OrderController::class);
Route::get('course/view/{id}', [CourseViewCountController::class, 'store']);

Route::apiResource('highest/revenue/course', HighestEarningCourseController::class);

Route::apiResource('event/attendance', EventAttendanceController::class);




Route::apiResource('questionnaires', QuestionnaireController::class);
Route::apiResource('question/templates', QuestionTemplateController::class);
// Question drafts
Route::get('question/drafts', [QuestionTemplateController::class, 'indexdraft']);
Route::put('question/drafts/{id}',  [QuestionTemplateController::class, 'updatedraft']);
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


Route::get('identical-members', [ConnectionController::class, 'getmemberswithinterests']);
Route::get('identical-facilitators', [ConnectionController::class, 'getfacilitatorswithinterests']);

Route::get('similar/users', [ConnectionController::class, 'getotherswithinterests']);



Route::get('other-discussions', [ConnectionController::class, 'getidenticaldiscusiions']);
Route::get('interest-courses', [ConnectionController::class, 'getidenticalcourses']);


// Mail routes
Route::get('send-mail', [MailController::class, 'sendwelcome']);
Route::post('send-referral', [MailController::class, 'sendreferral']);

Route::post('guest/send/invite', [MailController::class, 'guestsendcourseinvite']);
Route::post('send/invite', [MailController::class, 'sendcourseinvite']);
Route::post('send/discussion/invite', [MailController::class, 'senddiscussioninvite']);
Route::post('send/event/invite', [MailController::class, 'sendeventinvite']);

Route::get('guest/events/{id}', [EventController::class, 'guestevent']);
Route::get('guest/discussions/{id}', [DiscussionController::class, 'getguestdiscussion']);

Route::post('guest/send/discussion/invite', [MailController::class, 'senddiscussioninvite']);
Route::post('guest/send/event/invite', [MailController::class, 'sendeventinvite']);


//Referral routes

Route::apiResource('referrals', ReferralController::class);


// Notification request

Route::post('new/connection', [NotificationController::class, 'newconnection']);

Route::post('join-discussion', [NotificationController::class, 'joinDiscussionRequest']);

// Apply course
Route::apiResource('apply-community', CourseCommunityLinkController::class);

Route::apiResource('add-community', CourseCommunityController::class);


Route::apiResource('reviews', ReviewController::class);
Route::get('mostenrolled', [CourseController::class, 'mostenrolled']);
Route::get('member/mostenrolled', [CourseController::class, 'usermostenrolled']);
Route::get('toprated', [CourseController::class, 'toprated']);


// Guest
Route::get('guest/mostenrolled', [CourseController::class, 'guestmostenrolled']);
Route::get('guest/members', [UserController::class, 'index']);
Route::get('guest/facilitators', [FacilitatorController::class, 'guestindex']);
Route::get('guest/courses', [CourseController::class, 'guestcourses']);
Route::get('guest/discussions', [DiscussionController::class, 'guestdiscussions']);
Route::get('guest/explore/discussions', [DiscussionController::class, 'guestexplorediscussions']);
Route::get('guest/events', [EventController::class, 'guestindex']);
Route::get('guest/feeds', [FeedController::class, 'guestfeeds']);
Route::get('guest/trending/feeds', [FeedController::class, 'getTrendingFeedInterest']);
Route::get('guest/trending/feed/{interest}', [FeedController::class, 'getSpecificFeed']);

Route::get('guest/users/{interest}', [ConnectionController::class, 'getUsersWithInterest']);


// Contact message
Route::post('send/message', [MailController::class, 'contactmail']);

Route::apiResource('contributors', ContributorController::class);


Route::apiResource('discussion/private', PrivateDiscussionMemberController::class);
Route::apiResource('discussion/requests', DiscussionRequestController::class);
Route::post('discussion/reject', [NotificationController::class, 'discussionreject']);


Route::get('facilitator/feeds/{id}', [FacilitatorController::class, 'facilitatorfeeds']);
Route::get('facilitator/connections/{id}', [FacilitatorController::class, 'facilitatorconnections']);
Route::get('facilitator/discussions/{id}', [FacilitatorController::class, 'facilitatordiscussions']);
Route::get('facilitator/info/{id}', [FacilitatorController::class, 'facilitatorinfo']);
Route::get('facilitator/events/{id}', [FacilitatorController::class, 'facilitatorevents']);
Route::get('facilitator/courses/{id}', [FacilitatorController::class, 'facilitatorcourses']);

Route::get('member/feeds/{id}', [UserController::class, 'userfeeds']);
Route::get('member/connections/{id}', [UserController::class, 'userconnections']);
Route::get('member/discussions/{id}', [UserController::class, 'userdiscussions']);
Route::get('member/info/{id}', [UserController::class, 'userinfo']);
Route::get('member/events/{id}', [UserController::class, 'userevents']);


Route::get('get/feeds/tags', [FeedController::class, 'getFeedsByInterest']);


Route::get('get/interests/{interest}', [GuestController::class, 'getInterestContent']);
Route::get('get/members', [GuestController::class, 'getmembers']);


Route::post('forgot-password', [UserController::class, 'postEmail']);
Route::post('reset-password', [UserController::class, 'resetpassword']);


Route::post('update/information', [UserInformationController::class, 'update']);

// create or update a subscription for a user
Route::post('subscription', [SubscriptionController::class, 'store']);

// delete a subscription for a user
Route::post('subscription/delete', [SubscriptionController::class, 'destroy']);
Route::post('notify', [NotificationController::class, 'notify']);


// Tribe routes
Route::apiResource('tribes', TribeController::class);
Route::get('guest/tribes', [TribeController::class, 'guesttribes']);


Route::middleware(['auth:api'])->group(function () {
    Route::get('trending/discussions/{tribe}', [DiscussionController::class, 'tribetrenddiscussions']);
    Route::post('tribe/invite', [MailController::class, 'sendtribeinvite']);
    Route::get('user/tribes', [TribeController::class, 'getusertribe']);
    Route::get('tribe/members/{tribe}', [TribeController::class, 'tribemembers']);
    Route::get('check/tribe/{tribe}', [TribeController::class, 'checktribe']);
    Route::get('join/tribe/{tribe}', [TribeController::class, 'addusertotribe']);
    Route::get('leave/tribe/{tribe}', [TribeController::class, 'leavetribe']);
    Route::get('get/tribe/feeds/{tribe}', [TribeController::class, 'tribefeeds']);
    Route::get('get/tribe/courses/{tribe}', [TribeController::class, 'tribecourses']);
    Route::get('get/tribe/events/{tribe}', [TribeController::class, 'tribeevents']);
    Route::get('get/tribe/myevents/{tribe}', [TribeController::class, 'mytribeevents']);
    Route::get('get/tribe/discussions/{tribe}', [TribeController::class, 'tribediscussions']);
    Route::get('tribe/suggestions', [TribeController::class, 'suggestedtribe']);
    Route::post('vote/discussion/message', [DiscussionMessageController::class, 'votediscussionmessage']);
});

Route::get('search', [TribeSearchController::class, 'search']);
Route::get('all/tribes', [TribeSearchController::class, 'get']);


// Mobile Password
Route::post('generate/otp', [UserController::class, 'createotp']);
Route::post('password/reset', [UserController::class, 'changePasswordByOtp']);


//Bank Details

Route::get('get/banks', [BankDetailController::class, 'getbanks']);
Route::get('get/bank/detail', [BankDetailController::class, 'getbankdetail']);
Route::apiResource('bank/details', BankDetailController::class);
Route::post('transaction/initiate', [BankDetailController::class, 'makepayment']);

Route::get('transaction/verify/{reference}', [BankDetailController::class, 'verifytransaction']);
Route::post('transaction/verify', [BankDetailController::class, 'transactionevent']);
