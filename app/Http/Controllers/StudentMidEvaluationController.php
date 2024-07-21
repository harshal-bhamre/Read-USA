<?php

namespace App\Http\Controllers;

use App\Models\StudentMidEvaluation;
use Illuminate\Http\Request;

class StudentMidEvaluationController extends Controller
{
    public function create($id)
    {
        $studentId = decrypt($id);
        // $student = Student::findOrFail($studentId);
        return view("studentmidevaluation.create", compact("studentId"));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            "gort_assessed" => "required",
            "gort_assessment_date" => "required",
        ]);
        $input = $request->all();
        $input["student_id"] = decrypt($input["student_id"]);
        StudentMidEvaluation::create($input);
        return redirect('app/student/');
    }

    public function edit($id)
    {
        $studentId = decrypt($id);
        // dd($studentId);
        $studentMidEvaluation = StudentMidEvaluation::where('student_id', $studentId)->first();
        if ($studentMidEvaluation) {
            return view('studentmidevaluation.edit', compact('studentMidEvaluation'));
        } else {
            dd("student Mid Evaluation not found");
        }
    }

    public function update(Request $request)
    {
        // dd($request->all());
        // $request->validate([
        //     "word_test" => "required",
        //     "hand2mind" => "required",
        //     "garfieldassessment" => "required",
        //     "gortassessment" => "required",
        //     "ctoppassessment" => "required",
        //     "observation" => "required",
        // ]);

        $student_id = decrypt($request->input('student_id'));
        $condition = ["student_id" => $student_id];
        $input = $request->all();
        $input["student_id"] = $student_id;

        StudentMidEvaluation::updateOrCreate($condition, $input);

        return redirect('app/student/')->with('success', 'Mid evaluation updated successfully');

    }
}
