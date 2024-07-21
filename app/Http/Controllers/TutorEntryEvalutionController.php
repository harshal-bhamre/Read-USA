<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use App\Models\TutorEntryEvaluation;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TutorEntryEvalutionController extends Controller
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
        // dd($tutor);
        return view('tutorEntryEvalution.add', compact('tutor', 'userType'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $user = $this->user;
        // dd($user);
        $validator = Validator::make($request->all(),  [
            'gort' => 'required'
        ]);
        if ($validator->fails()) {
            dd($validator);
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $input = $request->all();
        TutorEntryEvaluation::create($input);
        if ($user->user_type == "A") {
            return redirect(url("tutor/list"))->with("success", " added successfully ");
        } else {
            return redirect(url("teacher/tutors/list"))->with("success", " added successfully ");
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $userType = $user->user_type;
        $id = decrypt($id);
        $tutor = TutorEntryEvaluation::where("tutor_id", $id)->first();
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => "/tutors", 'name' => "tutors"],
            ['link' => "javascript:void(0)", 'name' => "tutors/edit tutors_exit_survey"],
        ];

        return view('tutorEntryEvalution.edit', [
            'breadcrumbs' => $breadcrumbs,
            'tutor' => $tutor,
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
            dd($validator);
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $input = $request->all();
        $tutor_id = decrypt($request->input('tutor_id'));
        // dd($tutor_id);
        $condition = ["tutor_id" => $tutor_id];
        $input["tutor_id"] = $tutor_id;
        TutorEntryEvaluation::updateOrCreate($condition, $input);

        if ($user->user_type == "A") {
            return redirect(url("tutor/list"))->with("success", " added successfully ");
        } else {
            return redirect(url("teacher/tutors/list"))->with("success", " added successfully ");
        }
    }
}
