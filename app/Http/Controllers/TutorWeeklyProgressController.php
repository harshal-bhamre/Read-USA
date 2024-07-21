<?php

namespace App\Http\Controllers;

use App\Models\Tutor;
use App\Models\TutorWeeklyProgress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class TutorWeeklyProgressController extends Controller
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

    public function index(Request $request)
    {
        $user = auth()->user();
        // dd($user);

        if ($request->ajax()) {
            $data = TutorWeeklyProgress::select('*')
                ->join("tutors", "tutors.tutor_id", "=", "tutor_weekly_progress.tutor_id", "left")
                ->leftJoin("users as tutor_user", "tutor_user.id", "=", "tutors.user_id");

            if ($user->role == "T") {
                $data->leftJoin("teachers", "teachers.teacher_id", "=", "tutors.teacher_id")
                    ->leftJoin("users as teacher_user", "teacher_user.id", "=", "teachers.user_id")
                    ->where("teacher_user.id", $user->id);
                if ($request->get('filter_tutor')) {
                    $data->where("tutor_weekly_progress.tutor_id", $request->get('filter_tutor'));
                }
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit = '<a href="/php-laravel/tutor/' . "tutors_weekly_progress/edit/" . encrypt($row->tutorweeklyprogress_id) . '" data-toggle="tooltip" data-placement="top" title="Edit"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>   '
                        . '<a  id="deleteUser" class="text-primary ml-1" data-id="' . encrypt($row->tutorweeklyprogress_id) . '" data-model="TutorWeeklyProgress" data-toggle="tooltip" data-placement="top" title="Delete"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></i></a>';
                    return $edit;
                })

                ->filter(function ($instance) use ($request) {

                    if ($request->get('tutor')) {
                        $instance->where('tutor_user.name',  $request->get('tutor'));
                    }

                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('tutor_user.name', 'LIKE', "%$search%");
                        });
                    }
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        $tutors = User::where('user_type', 'TU')->get(['name']);
        // dd($tutors);
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => "javascript:void(0)", 'name' => "tutor Weekly Progress List"],
        ];
        return view('TutorWeekly.list', compact('tutors', 'breadcrumbs'));
    }
    public function create($id)
    {
        $user = auth()->user();
        $userType = $user->user_type;
        $tutorId = decrypt($id);
        $tutor = Tutor::findOrFail($tutorId);
        // dd($tutor);
        return view('tutorWeekly.add', compact('tutor', 'userType'));
    }

    public function store(Request $request)
    {
        $user = $this->user;
        // dd($user);
        $validator = Validator::make($request->all(), [
            'week'                      => ['required'],
            'lessons'                   => ['required'],
            'attendence_monday'         => ['required'],
            'attendence_tuesday'        => ['required'],
            'attendence_wednesday'      => ['required'],
            'attendence_thursday'       => ['required'],
            'attendence_friday'         => ['required'],
        ]);
        if ($validator->fails()) {
            dd($validator);
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $input = $request->all();
        // dd($input);
        $input['tutor_id'] = decrypt($input['tutor_id']);
        $tutors = TutorWeeklyProgress::create($input);
        if ($user->user_type == "A") {
            return redirect("tutor/list")->with("success", "Tutor's progress added");
        } else {
            return redirect("teacher/tutors/tutors_weekly_progress/list")->with("success", "Tutor's progress added");
        }
    }

    public function edit($id)
    {
        $tutor = TutorWeeklyProgress::select('*')->where("tutorweeklyprogress_id", decrypt($id))->first();

        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => "javascript:void(0)", 'name' => "tutor_weekly_progress Edit"],
        ];
        return view('TutorWeekly.edit', ['breadcrumbs' => $breadcrumbs, "tutor" => $tutor]);
    }

    public function update(Request $request)
    {
        $user = $this->user;
        $validator = Validator::make($request->all(), [
            // 'week'                      => ['required'],
            'lessons'                   => ['required'],
            'attendence_monday'         => ['required'],
            'attendence_tuesday'        => ['required'],
            'attendence_wednesday'      => ['required'],
            'attendence_thursday'       => ['required'],
            'attendence_friday'         => ['required'],
        ]);
        if ($validator->fails()) {
            dd($validator);
            return redirect()->back()->withErrors($validator)->withInput();
        }
        // dd($request->all());
        $input = $request->all();
        $searchInput["tutorweeklyprogress_id"] = $input["tutorweeklyprogress_id"];
        TutorWeeklyProgress::updateOrCreate($searchInput, $input);
        if ($user->user_type == "A") {
            return redirect("tutor/tutors_weekly_progress/list")->with("success", "Tutor's progress added");
        } else {
            return redirect("teacher/tutors/tutors_weekly_progress/list")->with("success", "Tutor's progress added");
        }
    }


    public function checkDate(Request $request)
    {
        // Log::info($request->all());
        log::info(decrypt($request->id));
        $id = $request->input('id');
        $start = $request->input('start');
        $end = $request->input('end');

        $progressReport = TutorWeeklyProgress::where('tutor_id', $id)
            ->whereBetween('week', [$start, $end])
            ->first();

        if ($progressReport) {

            return response()->json(['tutor_id' => $progressReport->id]);
        } else {
            // No progress report found
            return response()->json(null);
        }
    }
}
