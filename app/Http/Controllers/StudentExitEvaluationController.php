<?php

namespace App\Http\Controllers;

use App\Models\StudentExitEvaluation;
use Illuminate\Http\Request;

class StudentExitEvaluationController extends Controller
{
    public function create($id)
    {
        $studentId = decrypt($id);
        // $student = Student::findOrFail($studentId);
        return view("studentexitevaluation.create", compact("studentId"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "hand2mind" => "required",
            "garfieldassessment" => "required",
            "gortassessment" => "required",
            "ctoppassessment" => "required",
            "observation" => "required",
        ]);
        // dd($request->all());
        $input = $request->all();
        $input["student_id"] = decrypt($input["student_id"]);
        // dd($input);
        StudentExitEvaluation::create($input);
        return redirect('app/student/');
    }

    public function edit($id)
    {
        // dd(decrypt($id));
        $studentId = decrypt($id);
        // dd($studentId);
        $studentExitEvaluation = StudentExitEvaluation::where('student_id', $studentId)->first();
        if ($studentExitEvaluation) {
            return view('studentexitevaluation.edit', compact('studentExitEvaluation'));
        } else {
            dd("student Exit Evaluation not found");
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

        $student_id = $request->input('student_id');
        $condition = ["student_id" => $student_id];
        $input = $request->all();
        $input["student_id"] = $student_id;


        StudentExitEvaluation::updateOrCreate($condition, $input);

        return redirect('student/list')->with('success', 'Exit evaluation updated successfully');

    }
}
