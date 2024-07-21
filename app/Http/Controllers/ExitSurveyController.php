<?php

namespace App\Http\Controllers;

use App\Models\ExitSurvey;
use Illuminate\Http\Request;

class ExitSurveyController extends Controller
{
    public function create($id){
        $inputId = decrypt($id);
        return view("studentexitsurvey.create", compact('inputId'));
    }

    public function store(Request $request){
        // dd($request->all());
        $request->validate([
        'status' => 'required',
        'endate' => 'required',
        'sessions' => 'required'
        ]);

        $input = $request->all();
        // dd($input);
        ExitSurvey::create($input);
        return redirect('app/student/');
    }

    public function edit($id){
        $inputId = decrypt($id);
        // dd($inputId);
        // $exitSurvey = ExitSurvey::find($inputId);
        $exitSurvey = ExitSurvey::where('student_id', $inputId)->firstOrFail();
        // dd($exitSurvey);
        return view('studentexitsurvey.edit', compact('exitSurvey'));
    }

    public function update(Request $request){
        $request->validate([
            'status' => 'required',
            'endate' => 'required',
            'sessions' => 'required'
        ]);
        $input = $request->all();
    //   dd($input);
      $survey = ExitSurvey::find($input['exit_survey_id']);
      $searchInput['exit_survey_id'] = $request['exit_survey_id'];
      ExitSurvey::updateOrCreate($searchInput, $input);
      return redirect('app/student/');
    }
}
