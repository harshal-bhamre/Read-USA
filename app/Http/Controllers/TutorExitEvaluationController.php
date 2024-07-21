<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use App\Models\TutorExitEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TutorExitEvaluationController extends Controller
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
        // dd($userType);
        $tutor = Tutor::find(decrypt($id));
        return view('tutorExitEvalution.add', compact('tutor', 'userType'));
    }

    public function store(Request $request)
    {
        $user = $this->user;
        $validator = Validator::make($request->all(), [
            'gort' => 'required'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $input = $request->all();
        DB::beginTransaction();
        try {
            $std = TutorExitEvaluation::create($input);
            if ($std) {
                DB::commit();
                if ($user->user_type == "A") {
                    return redirect(url("tutor/list"))->with("success", " added successfully ");
                } else {
                    return redirect(url("teacher/tutors/list"))->with("success", " added successfully ");
                }
            } else {
                return redirect()->back()->with("error", "Something went wrong");
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with("error", "Something went wrong");
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $userType = $user->user_type;
        $id = decrypt($id);
        $tutor = TutorExitEvaluation::where("tutor_id", $id)->first();
        // dd($tutor);
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => "/tutors", 'name' => "tutors"],
            ['link' => "javascript:void(0)", 'name' => "Edit tutors/tutors_exit_survey"],
        ];

        return view('tutorExitEvalution.edit', [
            'breadcrumbs'   => $breadcrumbs,
            'tutor'         => $tutor,
            'userType'      => $userType,
        ]);
    }

    public function update(Request $request)
    {
        $user = $this->user;
        $validator = Validator::make($request->all(), [
            'gort' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $input = $request->all();
        // dd($input);
        $tutor_id = decrypt($request->input('tutor_id'));
        // dd($tutor_id);
        $condition = ["tutor_id" => $tutor_id];
        $input["tutor_id"] = $tutor_id;
        TutorExitEvaluation::updateOrCreate($condition, $input);
        if ($user->user_type == "A") {
            return redirect(url("tutor/list"))->with("success", " added successfully ");
        } else {
            return redirect(url("teacher/tutors/list"))->with("success", " added successfully ");
        }
    }
}
