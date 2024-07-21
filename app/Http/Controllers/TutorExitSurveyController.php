<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use App\Models\TutorExitSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TutorExitSurveyController extends Controller
{
    protected $user;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
    }
    public function create($id)
    {
        $user = auth()->user();
        $userType = $user->user_type;
        $tutor = Tutor::find(decrypt($id));
        return view('tutorExitSurvey.add', compact('tutor', 'userType'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'status'                => 'required',
            'readusa_lessons'       => 'required',
            'last_lesson'           => 'required'
        ]);

        if ($validator->fails()) {
            dd($validator);
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $inp = $request->all();
        DB::beginTransaction();
        try {
            $std = TutorExitSurvey::create($inp);
            // dd($std);

            if ($std) {
                DB::commit();
                if ($user->user_type == 'A') {
                    return redirect(url("tutor/list"))->with("success", "Student added successfully ");
                } else {
                    return redirect(url("teacher/tutors/list"))->with("success", " added successfully ");
                }
            } else {
                return redirect()->back()->with("error", "Something went wrong");
            }
        } catch (\Exception $e) {
            return redirect()->back()->with("error", "Something went wrong");
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $userType = $user->user_type;
        $id = decrypt($id);
        $tutor = TutorExitSurvey::where("tutor_id", $id)->first();
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => "tutor/list", 'name' => "tutors"],
            ['link' => "javascript:void(0)", 'name' => "tutors/tutors_exit_survey"],
        ];
        return view('tutorExitSurvey.edit', [
            'breadcrumbs'   => $breadcrumbs,
            'tutor'         => $tutor,
            'userType'      => $userType,
        ]);
    }

    public function update(Request $request)
    {
        $user = $this->user;
        // dd($user);
        $validator = Validator::make($request->all(), [
            'status'                => 'required',
            'readusa_lessons'       => 'required',
            'last_lesson'           => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $tutor_id = decrypt($request->input('tutor_id'));
        // dd($tutor_id);
        $input = $request->all();
        $condition = ["tutor_id" => $tutor_id];
        $input["tutor_id"] = $tutor_id;
        TutorExitSurvey::updateOrCreate($condition, $input);
        if ($user->user_type == "A") {
            return redirect(url("tutor/list"))->with("success", " added successfully ");
        } else {
            return redirect(url("teacher/tutors/list"))->with("success", " added successfully ");
        }
    }
}
