<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\sitecoordinator;
use App\Models\Teacher;
use App\Models\Tutor;
use App\Models\School;
use App\Models\Observer;
use App\Models\Student;



class DashboardController extends Controller
{
  // Dashboard - Analytics
  public function dashboardAnalytics()
  {
    $pageConfigs = ['pageHeader' => false];

    return view('/content/dashboard/dashboard-analytics', ['pageConfigs' => $pageConfigs]);
  }

  // Dashboard - Ecommerce
  public function dashboardEcommerce()
  {
    $pageConfigs = ['pageHeader' => false];
    $sitecoordinatorcount = sitecoordinator::count();
    $teachercount = Teacher::count();
    $tutorcount = Tutor::count();
    $schoolcount = School::count();
    $observercount = Observer::count();
    $studentscount = Student::count();
    return view('/content/dashboard/dashboard-ecommerce', compact('sitecoordinatorcount', 'teachercount', 'schoolcount', 'observercount', 'studentscount', 'tutorcount'), ['pageConfigs' => $pageConfigs]);
  }

  public function dashboardObserver()
  {
    $pageConfigs = ['pageHeader' => false];

    $totalSchool = School::count();

    $totalTeacher = Teacher::count();

    $totalTutor = Tutor::count();

    $totalStudent = Student::count();

    return view('/content/dashboard/observer-dashboard', compact('totalSchool', 'totalTeacher', 'totalTutor', 'totalStudent'), ['pageConfigs' => $pageConfigs]);
  }
}
