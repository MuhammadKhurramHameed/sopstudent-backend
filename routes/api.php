<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\ExamsController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\LmsController;
use App\Http\Controllers\ProgramRegistrationController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\payfastController;
use App\Http\Controllers\studentportalController;
use App\Http\Controllers\ContactController;


Route::middleware('cors')->group(function(){
    Route::any('/clear-cache', function() {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        return "Cache is cleared";
    });
    
    Route::any('savemydocument', [studentportalController::class, 'savemydocument']);
    Route::any('documentlist', [studentportalController::class, 'documentlist']);
    Route::any('savemyeducation', [studentportalController::class, 'savemyeducation']);
    Route::any('educationlist', [studentportalController::class, 'educationlist']);
    Route::any('savemycertificate', [studentportalController::class, 'savemycertificate']);
    
    Route::any('editmyeducation', [studentportalController::class, 'editmyeducation']);
    Route::any('deletemyeducation', [studentportalController::class, 'deletemyeducation']);
    
    Route::any('editmycertificate', [studentportalController::class, 'editmycertificate']);
    Route::any('deletemycertificate', [studentportalController::class, 'deletemycertificate']);
    
    Route::any('certificatelist', [studentportalController::class, 'certificatelist']);
    Route::any('faqlist', [studentportalController::class, 'faqlist']);
    Route::any('updateprofile', [studentportalController::class, 'updateprofile']);
    Route::any('contactlist', [studentportalController::class, 'contactlist']);
    Route::any('contactlist', [studentportalController::class, 'contactlist']);
    Route::any('quizdetails', [studentportalController::class, 'quizdetails']);
    Route::any('submitquizanswer', [studentportalController::class, 'submitquizanswer']);
    Route::any('finishquiz', [studentportalController::class, 'finishquiz']);
    Route::any('notifications', [studentportalController::class, 'notifications']);  //working
    Route::any('studentexamlist', [studentportalController::class, 'studentexamlist']);  //working
    Route::any('studentlmslist', [studentportalController::class, 'studentlmslist']);  //working
    Route::any('studentprogramlist', [studentportalController::class, 'studentprogramlist']);  //working
    Route::any('studentbatchlist', [studentportalController::class, 'studentbatchlist']);  //working
    Route::any('studentsubjectlist', [studentportalController::class, 'studentsubjectlist']);  //working
    Route::any('listOfAlbums', [studentportalController::class, 'listOfAlbums']); 

    Route::post('createTicket', [ContactController::class, 'createTicket']);
    Route::any('ticketlist', [ContactController::class, 'ticketlist']);

    Route::any('login', [loginController::class, 'login']);
    Route::any('register', [RegisterController::class, 'postRegisterApp']);  //working

    //verifyOTP
    Route::post('emailverification', [RegisterController::class, 'emailverification']);
    Route::any('generateOTP', [RegisterController::class, 'generateOTP']);
    Route::any('resetpassword', [RegisterController::class, 'resetpassword']);


// Route::middleware('login.check')->group(function(){	 

    Route::any('lms/{slug?}', [LmsController::class, 'lms']);  //working
    Route::any('lms/series/{slug}/{content_slug?}', [LmsController::class, 'viewItem']);  //working
    Route::any('lms-series', [LmsController::class, 'lmsSeries']);   //working
    
    Route::any('dashboard-top-records', [GeneralController::class, 'getTopExamAndLms']); //working
    Route::any('exam-categories', [GeneralController::class, 'examCategories']);  //working
    Route::any('lms-categories', [GeneralController::class, 'lmsCategories']);  //working
    Route::any('pages/{slug?}', [GeneralController::class, 'staticPages']);  //working
    Route::any('validate/coupon', [GeneralController::class, 'validateCoupon']);   //Error:500
    Route::any('payment-gateway/details', [GeneralController::class, 'gatewayDetails']);   //working
    Route::any('save-transaction', [GeneralController::class, 'saveTransaction']);   //working
    Route::any('get-currency-code', [GeneralController::class, 'getCurrencyCode']);  //working
    Route::any('get-payment-gateways', [GeneralController::class, 'getPaymentGateways']);  //working
    
    Route::any('user/profile/{id}', [UsersController::class, 'profile']);  //working
    Route::any('profile-image', [UsersController::class, 'uploadUserProfileImage']);
    Route::any('user/settings/{id}', [UsersController::class, 'settings']);  //working
    Route::any('user/update-password', [UsersController::class, 'updatePassword']);  //working
    Route::any('users/edit/{id}', [UsersController::class, 'update']);  //working
    Route::any('users/reset-password', [UsersController::class, 'resetUsersPassword']);  //notification
    Route::any('users/social-login', [UsersController::class, 'socialLoginUser']);  //notification
    Route::any('users/settings/{slug}', [UsersController::class, 'updateSettings']);  //incomplete function
    Route::any('bookmarks/delete/{bookmark_id}', [UsersController::class, 'deleteBookmarkById']);   //working
    Route::any('user/bookmarks/{id}', [UsersController::class, 'bookmarks']);   //working
    Route::any('user/subscriptions/{id}', [UsersController::class, 'paymentsHistory']);  //working
    Route::any('update-payment', [UsersController::class, 'updatePayment']);  //working
    Route::any('feedback/send', [UsersController::class, 'saveFeedBack']);  //working
    Route::any('update/user-sttings/{user_id}', [UsersController::class, 'updateUserPreferrenses']);  //code need to be optimized
    Route::any('instructions/{exam_slug}', [UsersController::class, 'instructions']);  //working
    Route::any('analysis/subject/{user_id}', [UsersController::class, 'subjectAnalysis']);  //working
    Route::any('analysis/exam/{user_id}', [UsersController::class, 'examAnalysis']);  //working
    Route::any('analysis/history/{user_id}/{exam_id?}', [UsersController::class, 'historyAnalysis']);  //working
    Route::any('bookmarks/save', [UsersController::class, 'saveBookmarks']);  //working
    Route::any('update-offline-payment', [UsersController::class, 'updateOfflinePayment']);   //working
    
    Route::any('exams/{slug?}', [ExamsController::class, 'exams']);  //checked(null value)
    Route::any('exams/student-exam-series/{slug}', [ExamsController::class, 'viewSeriesItem']);  //working
    Route::any('exam-series', [ExamsController::class, 'examSeries']);  //working
    Route::any('get-exam-questions/{slug}', [ExamsController::class, 'getQuestions']);   //working
    Route::any('finish-exam/{slug}', [ExamsController::class, 'finishExam']);   //notification
    Route::any('get-exam-key/{slug}', [ExamsController::class, 'getExamKey']);  //working

    Route::any('get-province', [ProvinceController::class, 'getProvince']);
    Route::any('get-district', [ProvinceController::class, 'getDistrict']);
    Route::any('get-grade', [ProvinceController::class, 'getGrade']);
    Route::any('get-program', [ProvinceController::class, 'getProgram']);
    Route::any('get-batch', [ProvinceController::class, 'getBatch']);

    Route::any('program-registration', [ProgramRegistrationController::class, 'register']);

    Route::get('getReferee/{id}', [UsersController::class, 'getReferee']);
    //profile's post API
    Route::post('generateCode', [UsersController::class, 'generateCode']);


    Route::get('alpaPay', [GeneralController::class, 'returnUrl']);
    Route::get('alpaPaylistener', [GeneralController::class, 'listenerUrl']);



    //payfast API
    Route::post('getAccessToken', [payfastController::class, 'getAccessToken']);

    Route::get('mail', [UsersController::class, 'mail']);

    //VerifyOTP

    
});
// });