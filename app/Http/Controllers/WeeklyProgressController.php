<?php

namespace App\Http\Controllers;

use App\Models\WeeklyProgress;
use Illuminate\Http\Request;

class WeeklyProgressController extends Controller
{
    public function create($id){
        $weeklyProgressId = decrypt($id);
        return view("studentweeklyprogress.create", compact('weeklyProgressId'));
    }

    public function store(Request $request){
        // $request->validate([
        // 'weekly' => 'required', 
        // 'fluency_this_week'  => 'required', 
        // 'identification_fluency_probe'  => 'required', 
        // 'fluency_score'  => 'required', 
        // 'student_fluency_this_week'  => 'required', 
        // 'students_fluency'  => 'required', 
        // 'book_level_fluency_rating'  => 'required', 
        // 'title_of_the_book_fluency'  => 'required',
        // 'self_corrections_book'  => 'required',
        // 'students_accuracy_score1'  => 'required',
        // 'students_fluency_score'  => 'required',
        // 'student_comprehension_score_within'  => 'required',
        // 'student_comprehension_score_beyond_about'  => 'required',
        // 'total_comprehension_score1'  => 'required',
        // 'instructional_level'  => 'required',
        // 'current_book_level'  => 'required',
        // 'attendance_monday'  => 'required',
        // 'attendance_tuesday'  => 'required',
        // 'attendance_wednesday'  => 'required',
        // 'attendance_thursday'  => 'required',
        // 'attendance_friday'  => 'required',
        // ]);
        $input = $request->all();
        // dd($input);
        WeeklyProgress::create($input);
        return redirect('/app/student/');
    }

    public function getBook($bookLevel){

    }
}
