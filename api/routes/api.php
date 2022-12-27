<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Crews\CrewController;
use App\Http\Controllers\Posts\PostController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Tasks\TaskController;
use App\Http\Controllers\Crews\InviteController;
use App\Http\Controllers\Channels\VideoController;
use App\Http\Controllers\Users\SettingsController;
use App\Http\Controllers\Channels\ChannelController;
use App\Http\Controllers\Comments\CommentController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\Channels\UploadVideoController;
use App\Http\Controllers\Projects\ProjectUploadController;
use App\Http\Controllers\Conversations\ConversationController;
use App\Http\Controllers\Users\OtherBrowserSessionsController;
use App\Http\Controllers\Users\TwoFactorAuthenticationController;

//FORTIFY ROUTES - 🔪DO NOT UNCOMMENT (REFERENCE ONLY)
//✔Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);
//✔Route::post('/login', [AuthenticatedSessionController::class, 'store']);
//✔Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
//✔Route::post('/register', [RegisteredUserController::class, 'store']);
//✔Route::post('/user/confirm-password', [ConfirmablePasswordController::class, 'store']);
//✔Route::get('/user/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show']);
//✔Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show']);
//✔Route::get('/user/two-factor-recovery-codes', [user/two-factor-recovery-codes::class, 'index']);
//✔Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store']);

//Route::post('/user/two-factor-recovery-codes', [user/two-factor-recovery-codes::class, 'store']);

//Route::post('/forgot-password', [PasswordResetLinkController::class, 'store']);
//Route::put('/user/password', [PasswordController::class, 'update']);
//Route::post('/reset-password', [NewPasswordController::class, 'store']);

//PUBLIC
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/project/{slug}', [ProjectController::class, 'findBySlug']);
Route::get('/search/projects', [ProjectController::class, 'search']);
Route::get('/search/users', [UserController::class, 'search']);
Route::get('/username/{username}', [UserController::class, 'findByUsername']);
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}/projects', [ProjectController::class, 'getForUser']);
Route::get('/crews/{id}/projects', [ProjectController::class, 'getForCrew']);
Route::get('/crew/{slug}', [CrewController::class, 'findBySlug']);
Route::put('/videos/{id}', [VideoController::class, 'show']);

Route::group(['middleware' => ['auth:sanctum']], function() {

    //USER
    Route::get('/user', [UserController::class, 'getMe']);
    Route::put('/user/profile-information', [SettingsController::class, 'updateUser']);
    Route::put('/user/settings/profile', [SettingsController::class, 'updateProfile']);
    Route::get('/users/{id}', [UserController::class, 'findById']);
    Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store']);
    Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy']);
    Route::get('/user/sessions', [OtherBrowserSessionsController::class, 'getSessions']);
    Route::delete('/user/sessions/purge', [OtherBrowserSessionsController::class, 'destroy']);

    //POSTS
    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{id}', [PostController::class, 'findPost']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    //POST SUPPORT
    Route::post('/posts/{id}/{supportable_type}', [PostController::class, 'support']);
    Route::get('/posts/{id}/supported', [PostController::class, 'checkIfUserIsSupporting']);

    //POST COMMENTS
    Route::post('/posts/{id}/comments', [CommentController::class, 'storePostComment']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

    //PROJECTS
    Route::post('/projects', [ProjectUploadController::class, 'upload']);
    Route::put('/projects/{id}', [ProjectController::class, 'update']);
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);
    Route::get('/projects/{id}', [ProjectController::class, 'findProject']);

    //PROJECT SUPPORT
    Route::post('/projects/{id}/{supportable_type}', [ProjectController::class, 'support']);
    Route::get('/projects/{id}/supported', [ProjectController::class, 'checkIfUserIsSupporting']);

    //PROJECT COMMENTS
    Route::post('/projects/{id}/comments', [CommentController::class, 'storeProjectComment']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

    //CREWS
    Route::get('/crews/{id}', [CrewController::class, 'findCrew']);
    Route::post('/crews', [CrewController::class, 'store']);
    Route::get('/crews', [CrewController::class, 'index']);
    Route::get('/users/crews', [CrewController::class, 'getUserCrews']);
    Route::get('/crews/{id}/projects', [ProjectController::class, 'getForCrew']);
    Route::put('/crews/{id}', [CrewController::class, 'update']);
    Route::delete('/crews/{id}', [CrewController::class, 'destroy']);
    Route::delete('/crews/{crew_id}/users/{user_id}', [CrewController::class, 'removeFromCrew']);

    //INVITES
    Route::post('/invites/{crew_id}', [InviteController::class, 'invite']);
    Route::post('/invites/{id}/resend', [InviteController::class, 'resend']);
    Route::post('/invites/{id}/respond', [InviteController::class, 'respond']);
    Route::delete('/invites/{id}', [InviteController::class, 'destroy']);

    //CONVERSATIONS
    Route::post('/conversations', [ConversationController::class, 'sendMessage']);
    Route::get('/conversations', [ConversationController::class, 'getUserConversations']);
    Route::get('/conversations/{id}/messages', [ConversationController::class, 'getConversationMessages']);
    Route::put('/conversations/{id}/mark-as-read', [ConversationController::class, 'markAsRead']);
    Route::delete('/conversations/{id}', [ConversationController::class, 'destroy']);

    //VIDEOS
    Route::get('/channels/{id}/videos', [UploadVideoController::class, 'index']);
    Route::post('/channels/{id}/videos', [UploadVideoController::class, 'store']);

    //VIDEO SUPPORT
    Route::post('/videos/{id}/{supportable_type}', [VideoController::class, 'support']);
    Route::get('/videos/{id}/supported', [VideoController::class, 'checkIfUserIsSupporting']);

    //VIDEO COMMENTS
    Route::post('/videos/{id}/comments', [CommentController::class, 'storeVideoComment']);
    Route::put('/comments/{id}', [CommentController::class, 'update']);
    Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

    //COMMENT SUPPORT
    Route::post('/comment/{id}/{supportable_type}', [CommentController::class, 'support']);
    Route::get('/comment/{id}/supported', [CommentController::class, 'checkIfUserIsSupporting']);

    //CHANNELS
    Route::get('/channels', [ChannelController::class, 'index']);

    //TASKS
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
    Route::get('/tasks/{id}', [TaskController::class, 'findTask']);
    Route::get('/tasks', [TaskController::class, 'index']);
});
