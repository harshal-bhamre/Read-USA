<?php

namespace App\Http\Controllers;

use App\Models\StudentEntryEvaluation;
use Illuminate\Http\Request;

class StudentEntryEvaluationController extends Controller
{
    public function create($id){
        $studentId = decrypt($id);
        return view('studententryevaluation.create', compact('studentId'));
    }

    public function store(Request $request){
        $request->validate([
            "word_test" => "required",
            "hand2mind" => "required",
            "garfieldassessment" => "required",
            "gortassessment" => "required",
            "ctoppassessment" => "required",
            "observation" => "required",
        ]);
        // dd($request->all());
        $input = $request->all();
        // $input["student_id"] = $input["student_id"];
        // dd($input);
        StudentEntryEvaluation::create($input);
        return redirect('app/student/');
    }

    public function edit($id)
    {
        // dd(decrypt($id));
        $studentId = decrypt($id);
        // dd($studentId);
        $studentEntryEvaluation = StudentEntryEvaluation::where('student_id', $studentId)->first();
        if ($studentEntryEvaluation) {
            return view('studententryevaluation.edit', compact('studentEntryEvaluation'));
        } else {
            dd("student Entry Evaluation not found");
        }
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $request->validate([
            "word_test" => "required",
            "hand2mind" => "required",
            "garfieldassessment" => "required",
            "gortassessment" => "required",
            "ctoppassessment" => "required",
            "observation" => "required",
        ]);


        $student_id = decrypt($request->input('student_id'));
        $condition = ["student_id" => $student_id];
        $input = $request->all();
        $input["student_id"] = $student_id;


        StudentEntryEvaluation::updateOrCreate($condition, $input);

        return redirect('app/student/')->with('success', 'Entry evaluation updated successfully');
    }

}
