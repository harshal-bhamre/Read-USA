<?php

use App\Http\Controllers\sitecoordinatorcontroller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\UserInterfaceController;
use App\Http\Controllers\CardsController;
use App\Http\Controllers\ComponentsController;
use App\Http\Controllers\ExtensionController;
use App\Http\Controllers\PageLayoutController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\MiscellaneousController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ChartsController;
use App\Http\Controllers\ExitSurveyController;
use App\Http\Controllers\CohortController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\ObserverController;
use App\Http\Controllers\SchoolController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\StudentEntryEvaluationController;
use App\Http\Controllers\StudentExitEvaluationController;
use App\Http\Controllers\StudentMidEvaluationController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\WeeklyProgressController;
use App\Http\Controllers\TutorEntryEvalutionController;
use App\Http\Controllers\TutorExitEvaluationController;
use App\Http\Controllers\TutorExitSurveyController;
use App\Http\Controllers\TutorWeeklyProgressController;

// Main Page Route
// Route::get('/', [DashboardController::class, 'dashboardEcommerce']);
// Auth::routes(['verify' => true]);

Route::get('/', [DashboardController::class, 'dashboardEcommerce'])->middleware("auth");
Auth::routes(['verify' => true]);

/* Route Authentication Pages */
Route::group(['prefix' => 'auth'], function () {
  Route::get('login-v1',                    [AuthenticationController::class, 'login_v1']);
  Route::post('login-v1',                   [AuthenticationController::class, 'login']);
  Route::get('register-v1',                 [AuthenticationController::class, 'register_v1'])->name('auth-register-v1');
  Route::POST('/student',                   [AuthenticationController::class, 'student']);
  Route::get('register-v2',                 [AuthenticationController::class, 'register_v2'])->name('auth-register-v2');
  Route::get('forgot-password-v1',          [AuthenticationController::class, 'forgot_password_v1'])->name('auth-forgot-password-v1');
  Route::post('forgot-password-v1',         [AuthenticationController::class, 'forgotPassword']);
  Route::get('reset-password-v1',           [AuthenticationController::class, 'reset_password_v1'])->name('auth-reset-password-v1');
  Route::post('reset-password',             [AuthenticationController::class, 'resetPassword']);
});


/* Route Dashboards */
Route::group(['prefix' => 'dashboard'], function () {
  Route::get('analytics',                   [DashboardController::class, 'dashboardAnalytics'])->name('dashboard-analytics');
  Route::get('ecommerce',                   [DashboardController::class, 'dashboardEcommerce']);
});


//sitecoordinator
Route::group(['prefix' => 'siteCoordinator'], function () {
  Route::get('list',                        [sitecoordinatorcontroller::class, 'index']);
  Route::get('add',                         [sitecoordinatorcontroller::class, 'create']);
  Route::get('edit/{id}',                   [sitecoordinatorcontroller::class, 'edit']);
  Route::get('view/{id}',                   [sitecoordinatorcontroller::class, 'view']);
  Route::get('export',                      [SitecoordinatorController::class, 'export']);
  Route::post('store',                      [sitecoordinatorcontroller::class, 'store']);
  Route::post('update',                     [sitecoordinatorcontroller::class, 'update']);
});

Route::group(['prefix' => 'common'], function () {
  Route::post('delete/',                    [CommonController::class, 'delete']);
});

//teacher
Route::group(['prefix' => 'teacher'], function () {
  Route::get('list/{id?}',                  [TeachersController::class, 'index']);
  Route::get('add',                         [TeachersController::class, 'create']);
  Route::get('edit/{id}',                   [TeachersController::class, 'edit']);
  Route::get('view/{id}',                   [TeachersController::class, 'view']);
  Route::get('export',                      [TeachersController::class, 'export']);
  Route::get('getSchoolsByUniversity',      [TeachersController::class, 'getSchoolsByUniversity']);
  Route::post('store',                      [TeachersController::class, 'store']);
  Route::post('update',                     [TeachersController::class, 'update']);
});


//Tutor
Route::group(['prefix' => 'tutor'], function () {
  Route::get('list/{id?}',                  [TutorController::class, 'index']);
  Route::get('add',                         [TutorController::class, 'create']);
  Route::get('getSchoolsByUniversity',      [TeachersController::class, 'getSchoolsByUniversity']);
  Route::get('getTeachersBySchool',         [TutorController::class, 'getTeachersBySchool']);
  Route::post('store',                      [TutorController::class, 'store']);
  Route::get('edit/{id}',                   [TutorController::class, 'edit']);
  Route::post('update',                     [TutorController::class, 'update']);
  Route::get('export',                      [TutorController::class, 'export']);
  Route::get('deletedTutor',                [TutorController::class, 'deletedTutor']);
  Route::post('downloaddata',               [TutorController::class, 'download_data']);

  Route::group(['prefix' => 'tutors_weekly_progress'], function () {
    Route::get('list',                      [TutorWeeklyProgressController::class, 'index']);
    Route::get('add/{id}',                  [TutorWeeklyProgressController::class, 'create']);
    Route::post('store',                    [TutorWeeklyProgressController::class, 'store']);
    Route::get('edit/{id}',                 [TutorWeeklyProgressController::class, 'edit']);
    Route::post('update',                   [TutorWeeklyProgressController::class, 'update']);
    Route::post('/checkdate',               [TutorWeeklyProgressController::class, 'checkDate']);
  });

  Route::group(['prefix' => 'tutors_exit_survey'], function () {
    Route::get('add/{id}',                  [TutorExitSurveyController::class, 'create']);
    Route::post('store',                    [TutorExitSurveyController::class, 'store']);
    Route::get('edit/{id}',                 [TutorExitSurveyController::class, 'edit']);
    Route::post('update',                   [TutorExitSurveyController::class, 'update']);
  });

  Route::group(['prefix' => 'tutors_evaluation'], function () {
    Route::get('add/{id}',                  [TutorEntryEvalutionController::class, 'create']);
    Route::post('add',                      [TutorEntryEvalutionController::class, 'store']);
    Route::get('edit/{id}',                 [TutorEntryEvalutionController::class, 'edit']);
    Route::post('update',                   [TutorEntryEvalutionController::class, 'update']);
  });

  Route::group(['prefix' => 'tutors_exit_evaluation'], function () {
    Route::get('add/{id}',                  [TutorExitEvaluationController::class, 'create']);
    Route::post('store',                    [TutorExitEvaluationController::class, 'store']);
    Route::get('edit/{id}',                 [TutorExitEvaluationController::class, 'edit']);
    Route::post('update',                   [TutorExitEvaluationController::class, 'update']);
  });
});

Route::group(['prefix' => 'cohort'], function () {
  Route::get('list/{id?}',                  [CohortController::class, 'index']);
  Route::get('add',                         [CohortController::class, 'create']);
  Route::post('store',                      [CohortController::class, 'store']);
  Route::get('edit/{id}',                   [CohortController::class, 'edit']);
  Route::post('update',                     [CohortController::class, 'update']);
});



/* Route Apps */
Route::group(['prefix' => 'app'], function () {
  //Observer
  Route::group(['prefix' => 'observer'], function () {
    Route::get('', [ObserverController::class, 'index']);
    Route::get('add', [ObserverController::class, 'create']);
    Route::post('create', [ObserverController::class, 'store']);
    Route::get('edit/{id}', [ObserverController::class, 'edit']);
    Route::post('update', [ObserverController::class, 'update']);
    Route::get('view/{id}', [ObserverController::class, 'view']);
  });

  //Schools
  Route::group(['prefix' => 'schools'], function () {
    Route::get('add', [SchoolController::class, 'create']);
    Route::post('create', [SchoolController::class, 'store']);
    Route::get('edit/{id}', [SchoolController::class, 'edit']);
    Route::post('update', [SchoolController::class, 'update']);
    Route::get('export', [ExportController::class, 'export']);
    Route::get('fetch-schools/{universityId}', [SchoolController::class, 'fetchSchools']);
    Route::get('/{id?}', [SchoolController::class, 'index']);
  });

  //Students
  Route::group(['prefix' => 'student'], function () {
    Route::get('create', [StudentController::class, 'create']);
    Route::post('store', [StudentController::class, 'store']);
    Route::get('edit/{id}', [StudentController::class, 'edit']);
    Route::post('update', [StudentController::class, 'update']);
    Route::get('/{id?}', [StudentController::class, 'index']);
    //WeeklyProgress
    Route::get('addWeeklyProgress/{id}', [WeeklyProgressController::class, 'create']);
    Route::post('createWeeklyProgress', [WeeklyProgressController::class, 'store']);
    Route::get('getBookTitle', [WeeklyProgressController::class, 'getBook']);
    //EntrySurvey
    Route::get('entry_survey/{id}', [StudentController::class, 'entrySurvey']);
    //ExitSurvey
    Route::get('add_exit_survey/{id}', [ExitSurveyController::class, 'create']);
    Route::post('create', [ExitSurveyController::class, 'store']);
    Route::get('edit_exit_survey/{id}', [ExitSurveyController::class, 'edit']);
    Route::post('update', [ExitSurveyController::class, 'update']);
    //EntryEvaluation
    Route::get('add_entry_evaluation/{id}', [StudentEntryEvaluationController::class, 'create']);
    Route::post('create', [StudentEntryEvaluationController::class, 'store']);
    Route::get('edit_entry_evaluation/{id}', [StudentEntryEvaluationController::class, 'edit']);
    Route::post('update', [StudentEntryEvaluationController::class, 'update']);
    //MidEvaluation
    Route::get('add_mid_evaluation/{id}', [StudentMidEvaluationController::class, 'create']);
    Route::post('create', [StudentMidEvaluationController::class, 'store']);
    Route::get('edit_mid_evaluation/{id}', [StudentMidEvaluationController::class, 'edit']);
    Route::post('update', [StudentMidEvaluationController::class, 'update']);
    //ExitEvaluation
    Route::get('add_exit_evaluation/{id}', [StudentExitEvaluationController::class, 'create']);
    Route::post('create', [StudentExitEvaluationController::class, 'store']);
    Route::get('edit_exit_evaluation/{id}', [StudentExitEvaluationController::class, 'edit']);
    Route::post('update', [StudentExitEvaluationController::class, 'update']);
    //Export
    Route::post('export', [StudentController::class, 'export']);
  });
});



// for teacher login 
Route::group(['prefix' => 'teacher'], function () {
  Route::get('/dashboard',         [DashboardController::class, 'dashboardEcommerce']);
  Route::get('tutor',              [TutorController::class, 'index']);

  Route::group(['prefix' => 'tutors'], function () {

    Route::group(['prefix' => 'tutors_weekly_progress'], function () {
      Route::get('list',          [TutorWeeklyProgressController::class, 'index']);
      Route::get('add/{id}',      [TutorWeeklyProgressController::class, 'create']);
      Route::post('store',        [TutorWeeklyProgressController::class, 'store']);
      Route::get('edit/{id}',     [TutorWeeklyProgressController::class, 'edit']);
      Route::post('update',       [TutorWeeklyProgressController::class, 'update']);
    });

    Route::group(['prefix' => 'tutors_exit_survey'], function () {
      // Route::get('/{id}',[TutorExitSurveyController::class,'index']);
      Route::get('add/{id}',      [TutorExitSurveyController::class, 'create']);
      Route::post('add',          [TutorExitSurveyController::class, 'store']);
      Route::get('edit/{id}',     [TutorExitSurveyController::class, 'edit']);
    });

    Route::group(['prefix' => 'tutors_evaluation'], function () {
      Route::get('add/{id}',    [TutorEntryEvalutionController::class, 'create']);
      Route::post('add',        [TutorEntryEvalutionController::class, 'store']);
      Route::get('edit/{id}',   [TutorEntryEvalutionController::class, 'edit']);
    });

    Route::group(['prefix' => 'tutors_exit_evaluation'], function () {
      Route::get('add/{id}', [TutorExitEvaluationController::class, 'create']);
      Route::post('add', [TutorExitEvaluationController::class, 'store']);
      Route::get('edit/{id}', [TutorExitEvaluationController::class, 'edit']);
    });

    Route::get('add', [TutorController::class, 'create']);
    Route::post('add', [TutorController::class, 'store']);
    Route::get('edit/{id}', [TutorController::class, 'edit']);
    Route::post('edit', [TutorController::class, 'update']);
    Route::get('export', [TutorController::class, 'export']);
    Route::get('delete_data', [TutorController::class, 'delete_data']);
    Route::post('downloaddata', [TutorController::class, 'download_data']);
    Route::get('list/{id?}', [TutorController::class, 'index']);
  });
});



// for observer login 
Route::group(["prefix" => "observer"], function () {
  Route::get('/dashboard', [DashboardController::class, 'dashboardObserver']);

  Route::group(["prefix" => "schools"], function () {
    Route::get('/{id?}', [SchoolController::class, 'index']);
  });

  Route::group(["prefix" => "teachers"], function () {
    Route::get('list/{id?}',                  [TeachersController::class, 'index']);
  });

  Route::group(["prefix" => "tutors"], function () {
    Route::get('list/{id?}',                  [TutorController::class, 'index']);
    Route::get('edit/{id}',                   [TutorExitSurveyController::class, 'edit']);
  });

  Route::group(["prefix" => "tutorExitSurvey"], function () {
    Route::get('edit/{id}',                   [TutorExitSurveyController::class, 'edit']);
  });

  Route::group(["prefix" => "tutorEntryEvalution"], function () {
    Route::get('edit/{id}',                   [TutorEntryEvalutionController::class, 'edit']);
  });

  Route::group(["prefix" => "tutorExitEvalution"], function () {
    Route::get('edit/{id}',                   [TutorExitEvaluationController::class, 'edit']);
  });

  Route::group(["prefix" => "viewStudent"], function () {
  });
});
