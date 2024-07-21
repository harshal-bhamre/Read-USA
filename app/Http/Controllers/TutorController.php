<?php

namespace App\Http\Controllers;

use App\Exports\TutorEntryEvaluationExport;
use App\Exports\TutorExitEvaluationExport;
use App\Exports\TutorExitSurveyExport;
use App\Exports\TutorExport;
use App\Exports\TutorWeeklyProgressExport;
use App\Models\Detail;
use App\Models\School;
use App\Models\sitecoordinator;
use App\Models\Teacher;
use App\Models\Tutor;
use App\Models\TutorEntryEvaluation;
use App\Models\TutorEntryEvalution;
use App\Models\TutorExitEvaluation;
use App\Models\TutorExitEvalution;
use App\Models\TutorExitSurvey;
use App\Models\User;
use Exception;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class TutorController extends Controller
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

    public function index(Request $request, $id = null)
    {
        $user = auth()->user();
        // dd($user);

        if ($request->ajax()) {
            $data = Tutor::select('tutors.*', 'tutor_user.*', 'teacher_user.name as teacher_name', 'sitecoordinator.*', 'school_name', 'tutors.created_at as create_date')
                ->join("users as tutor_user", "tutor_user.id", "=", "tutors.user_id", "left")
                ->join("teachers", "teachers.teacher_id", "=", "tutors.teacher_id", "left")
                ->join("users as teacher_user", "teacher_user.id", "=", "teachers.user_id", "left")
                ->join("schools", "schools.school_id", "=", "teachers.school_id", "left")
                ->join("sitecoordinator", "sitecoordinator.university_id", "=", "schools.university_id", "left");

            if ($id) {
                $data->where("teachers.teacher_id", decrypt($id));
            }

            if ($request->get('site_coordinator')) {
                $data->where('university_name', $request->get('site_coordinator'));
            }

            if ($request->get('teacher')) {
                $data->where('teacher_user.name',  $request->get('teacher'));
            }

            if ($request->get('school')) {
                $data->where('school_name',  $request->get('school'));
            }

            if ($request->get('status')) {
                $data->where('tutor_user.status',  $request->get('status'));
            }

            if ($user->user_type == "T") {
                $data->where("teachers.user_id", $user->id);
            }

            if (!empty($request->get('search'))) {
                $data->where(function ($w) use ($request) {
                    $search = $request->get('search');
                    $w->orWhere('tutor_user.name', 'LIKE', "%$search%");
                });
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn("study_group", function () {
                    return "Read Usa";
                })
                ->addColumn(
                    "weekly_progress",
                    function ($row) use ($user) {
                        if ($user->user_type == 'T') {
                            $btn = '<a class="mr-1" href="/php-laravel/teacher/tutors' . "/tutors_weekly_progress/add/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="view">Add</a>';
                            return $btn;
                        } else {
                            $btn = '<a class="mr-1" href="/php-laravel/tutor' . "/tutors_weekly_progress/add/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="view">Add</a>';
                            return $btn;
                        }
                    }
                )

                ->addColumn("exit_survey", function ($row) use ($user) {
                    $tutorExitSurvey = TutorExitSurvey::where("tutor_id", $row->tutor_id)->first();
                    if ($user->user_type == 'O') {
                        if ($tutorExitSurvey) {
                            $btn = '<a class="mr-1" href="/php-laravel/observer/tutorExitSurvey/' . "edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="Edit Survey">View</a>';
                        } else {
                            $btn = 'NA';
                        }
                    } else if ($user->user_type == 'T') {
                        if ($tutorExitSurvey) {
                            $btn = '<a class="mr-1" href="/php-laravel/teacher/tutors' . "/tutors_exit_survey/edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="Edit Exit Survey"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>';
                        } else {
                            $btn = '<a class="mr-1" href="/php-laravel/teacher/tutors' . "/tutors_exit_survey/add/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="Add Survey">Add</a>';
                        }
                    } else {
                        if ($tutorExitSurvey) {
                            $btn = '<a class="mr-1" href="/php-laravel/tutor' . "/tutors_exit_survey/edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="Edit Exit Survey"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>';
                        } else {
                            $btn = '<a class="mr-1" href="/php-laravel/tutor' . "/tutors_exit_survey/add/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="Add Survey">Add</a>';
                        }
                    }
                    return $btn;
                })

                ->addColumn("entry_evaluation", function ($row) use ($user) {
                    $tutorEntry = TutorEntryEvaluation::where("tutor_id", $row->tutor_id)->first();
                    if ($user->user_type == 'O') {
                        if ($tutorEntry) {
                            $btn = '<a class="mr-1" href="/php-laravel/observer/tutorEntryEvalution/' . "edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="Edit Survey">View</a>';
                        } else {
                            $btn = 'NA';
                        }
                    } else if ($user->user_type == 'T') {
                        if ($tutorEntry) {
                            $btn = '<a class="mr-1" href="/php-laravel/teacher/tutors' . "/tutors_evaluation/edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="view"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>';
                        } else {
                            $btn = '<a class="mr-1" href="/php-laravel/teacher/tutors' . "/tutors_evaluation/add/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="view">Add</a>';
                        }
                    } else {
                        if ($tutorEntry) {
                            $btn = '<a class="mr-1" href="/php-laravel/tutor' . "/tutors_evaluation/edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="view"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>';
                        } else {
                            $btn = '<a class="mr-1" href="/php-laravel/tutor' . "/tutors_evaluation/add/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="view">Add</a>';
                        }
                    }
                    return $btn;
                })

                ->addColumn("exit_evaluation", function ($row) use ($user) {
                    $tutorExit = TutorExitEvaluation::where("tutor_id", $row->tutor_id)->first();
                    // \Log::info($tutorExit);
                    if ($user->user_type == 'O') {
                        if ($tutorExit) {
                            $btn = '<a class="mr-1" href="/php-laravel/observer/tutorExitEvalution/' . "edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="Edit Survey">View</a>';
                        } else {
                            $btn = 'NA';
                        }
                    } else if ($user->user_type == 'T') {
                        if ($tutorExit) {
                            $btn = '<a class="mr-1" href="/php-laravel/teacher/tutors' . "/tutors_exit_evaluation/edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="Edit Exit Survey"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>';
                        } else {
                            $btn = '<a class="mr-1" href="/php-laravel/teacher/tutors' . "/tutors_exit_evaluation/add/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="Add Survey">Add</a>';
                        }
                    } else {
                        if ($tutorExit) {
                            $btn = '<a class="mr-1" href="/php-laravel/tutor/' . "tutors_exit_evaluation/edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="view"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>';
                        } else {
                            $btn = '<a class="mr-1" href="/php-laravel/tutor/' . "tutors_exit_evaluation/add/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="view">Add</a>';
                        }
                    }
                    return $btn;
                })

                ->addColumn('action', function ($row) use ($user) {
                    if ($user->user_type == 'A') {
                        $btn = '<a href="/php-laravel/tutor/' . "edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="Edit"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>   '
                            . '<a  id="deleteUser" class="text-primary ml-1" data-id="' . encrypt($row->tutor_id) . '"  data-model="Tutor" data-toggle="tooltip" data-placement="top" title="Delete"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></i></a>';
                    } else {
                        $btn = '<a href="/php-laravel/teacher/tutors/' . "edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="Edit"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>   '
                            . '<a  id="deleteUser" class="text-primary ml-1" data-id="' . encrypt($row->tutor_id) . '"  data-model="Tutor" data-toggle="tooltip" data-placement="top" title="Delete"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></i></a>';
                    }
                    return $btn;
                })

                ->addColumn('view', function ($row) {
                    $view = '<a href="/php-laravel/app/' . "student/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="View"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></i></a>';
                    return $view;
                })

                ->rawColumns(['action', 'view', 'weekly_progress', 'study_group', 'entry_evaluation', 'exit_survey', 'exit_evaluation'])
                ->make(true);
        }
        $siteCoordinators = User::where('user_type', 'SC')->get(['name']);
        $teachers         = User::where('user_type', 'T')->get(['name']);
        $schools          = School::select('*')->get(['school_name']);
        $userType         = $user->user_type;
        // dd($userType);
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => "javascript:void(0)", 'name' => "Tutor List"],
        ];
        return view('Tutor.list', compact('siteCoordinators', 'teachers', 'schools', 'userType', 'breadcrumbs'));
    }

    public function create()
    {
        $user = auth()->user();
        $userType = $user->user_type;
        $siteCoordinators = sitecoordinator::all();
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => "javascript:void(0)", 'name' => "Add Tutor"],
        ];
        return view('Tutor.add', compact('siteCoordinators', 'breadcrumbs', 'userType'));
    }

    public function getTeachersBySchool(Request $request)
    {
        if ($request->ajax()) {
            $schoolId = $request->schoolId;
            $teachers = Teacher::select("teacher_id", "users.name")
                ->leftJoin("users", "users.id", "=", "teachers.user_id")
                ->where('school_id', $schoolId)->get();

            return response()->json($teachers);
        }
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $role = $user->user_type;
        $input = $request->all();
        dd($input);

        $validator = Validator::make($request->all(), [
            "name"              => ["required"],
            "phone"             => ["required"],
            "email"             => ["required"],
            "password"          => ["required"],
            "tutoring_start_date"        => ["required"],
            "district"          => ["required"],
            "zip_code"          => ["required"],
            "gender"            => ["required"],
            "hispanic"          => ["required"],
            "race"              => ["required"],
        ]);

        if ($this->user->user_type == 'A') {
            $validator = Validator::make($request->all(), [
                "university_id"     => ["required"],
                "school_id"         => ["required"],
                "teacher_id"        => ["required"],
            ]);
        }

        if ($validator->fails()) {
            dd($validator);
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $input = $request->all();
        dd($input);
        if ($this->user->user_type == 'T') {
            $teacherId = Teacher::where("user_id", "=", $this->user->id);
            $input['teacher_id'] = $teacherId->first()->teacher_id;
        }
        // else {
        // $input['teacher_id'] = $input["teacher"];
        // }
        DB::beginTransaction();

        $input["password"] = Hash::make($input["password"]);
        $input["user_type"] = "TU";
        $input["status"] = "active";

        if ($request->has('race')) {
            $input["race"] = join(",", $input["race"]);
        }

        try {
            $user = User::create($input);
            $details = Detail::create($input);

            $input['user_id'] = $user->id;
            $input['details_id'] = $details->details_id;
            dd($input);
            Tutor::create($input);
            DB::commit();
            // dd($role);
            if ($role == "A") {
                return redirect(url("/tutor/list"))->with("success", "Added successfully Successfully");
            } else {
                return redirect(url("teacher/tutors/list"))->with("success", "Added successfully Successfully");
            }
        } catch (Exception $e) {
            return redirect()->back()->with("error", "DB ERROR");
        }
    }

    // work in progress 
    public function edit(Request $request, $id)
    {
        $user = auth()->user();
        $userType = $user->user_type;
        $id = decrypt($id);
        // dd($id);
        // dd($request->all());
        $tutor = Tutor::find($id);
        $tutor = Tutor::select("*")->join("users", "users.id", "=", "tutors.user_id")
            ->join("details", "details.details_id", "=", "tutors.details_id")->where("tutor_id", $tutor["tutor_id"])->first();
        $teacher = Teacher::select("teacher_id", "users.name", "school_id")->join("users", "users.id", "=", "teachers.user_id")->where("teacher_id", $tutor->teacher_id)->first();
        // dd($teacher);
        $tutor["tutor"]         = $tutor->name;
        $tutor["teacher_id"]    = $tutor->teacher_id;
        $tutor["teacher"]       = $teacher->name;

        $school = School::find($teacher->school_id);
        $tutor["school_id"]     = $teacher->school_id;
        $site = SiteCoordinator::find($school->university_id);
        $tutor["university_id"] = $site->university_id;
        // dd($tutor);
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => "javascript:void(0)", 'name' => "Edit tutor"],
        ];
        return view('Tutor.edit', [
            "site"          => $site,
            'breadcrumbs'   => $breadcrumbs,
            "school"        => $school,
            "tutor"         => $tutor,
            "teacher"       => $teacher,
            "userType"      => $userType,
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $role = $user->user_type;
        $validator = Validator::make($request->all(), [
            "teacher"           => ["required"],
            "name"              => ["required"],
            "phone"             => ["required"],
            "email"             => ["required"],
            "district"          => ["required"],
            "zip_code"          => ["required"],
            "gender"            => ["required"],
            "race"              => ["required"],
        ]);

        if ($validator->fails()) {
            dd($validator);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $inp = $request->all();

        $inp["race"] = join(",", $inp["race"]);
        // dd($inp);

        $searchInput["tutor_id"] = $inp["tutor_id"];
        DB::beginTransaction();
        try {

            $tutor = Tutor::updateOrCreate($searchInput, $inp);
            $inp["user_type"] = "TU";
            $inp["status"] = "active";

            $searchInputDetails["details_id"] = $tutor->details_id;
            $searchInputUser["id"] = $tutor->user_id;

            $details = Detail::updateOrCreate($searchInputDetails, $inp);
            $user = User::updateOrCreate($searchInputUser, $inp);
            Db::commit();
            if ($role == "A") {
                return redirect(url("/tutor/list"))->with("success", "Added successfully Successfully");
            } else {
                return redirect(url("teacher/tutors/list"))->with("success", "Added successfully Successfully");
            }
        } catch (Exception $e) {
            dd($e);
        }
    }

    public function export()
    {
        return Excel::download(new TutorExport, 'tutors.xlsx');
    }

    public function download_data(Request $request)
    {
        $inp = $request->all();
        // dd($inp);
        switch ($inp["form-select"]) {
            case '1':
                return Excel::download(new TutorEntryEvaluationExport, 'Tutor_EntryEvaluation.xlsx');

            case '2':
                return Excel::download(new TutorExitEvaluationExport, 'Tutor_ExitEvaluation.xlsx');

            case 3:

                return Excel::download(new TutorExitSurveyExport, 'Tutor_ExitSurvey.xlsx');;
            case 4:

                return Excel::download(new TutorWeeklyProgressExport, 'Entry_Survey.xlsx');;
        }
    }

    public function deletedTutor(Request $request)
    {
        $user = auth()->user();
        if ($request->ajax()) {

            $data = Tutor::select(
                'tutors.*',
                'tutor_user.*',
                'teacher_user.name as teacher_name',
                'sitecoordinator.*',
                'school_name',
                'tutors.created_at as create_date'
            )
                ->withTrashed()
                ->whereNotNull('tutors.deleted_at')
                ->join("users as tutor_user", "tutor_user.id", "=", "tutors.user_id", "left")
                ->join("teachers", "teachers.teacher_id", "=", "tutors.teacher_id", "left")
                ->join("users as teacher_user", "teacher_user.id", "=", "teachers.user_id", "left")
                ->join("schools", "schools.school_id", "=", "teachers.school_id", "left")
                ->join("sitecoordinator", "sitecoordinator.university_id", "=", "schools.university_id", "left");

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn("study_group", function () {
                    return "Read Usa";
                })
                ->addColumn(
                    "weekly_progress",
                    function ($row) {
                        return '<a class="mr-1" href="/php-laravel/tutor' . "/tutors_weekly_progress/add/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="view">Add</a>';
                    }
                )
                ->addColumn("exit_survey", function ($row) {
                    $tutorExit = TutorExitSurvey::where("tutor_id", $row->tutor_id)->first();
                    if ($tutorExit) {
                        $btn = '<a class="mr-1" href="/php-laravel/tutor' . "/tutors_exit_survey/edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="Edit Exit Survey"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>';
                    } else {
                        $btn = '<a class="mr-1" href="/php-laravel/tutor' . "/tutors_exit_survey/add/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="Add Survey">Add</a>';
                    }
                    return $btn;
                })
                ->addColumn("entry_evaluation", function ($row) {
                    $tutorEntry = TutorEntryEvaluation::where("tutor_id", $row->tutor_id)->first();
                    if ($tutorEntry) {
                        $btn = '<a class="mr-1" href="/php-laravel/tutor' . "/tutors_evaluation/edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="view"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>';
                    } else {
                        $btn = '<a class="mr-1" href="/php-laravel/tutor' . "/tutors_evaluation/add/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="view">Add</a>';
                    }
                    return $btn;
                })
                ->addColumn("exit_evaluation", function ($row) {

                    $tutorExit = TutorExitEvaluation::where("tutor_id", $row->tutor_id)->first();
                    // \Log::info($tutorExit);
                    if ($tutorExit) {
                        $btn = '<a class="mr-1" href="/php-laravel/tutor/' . "tutors_exit_evaluation/edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="view"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>';
                    } else {
                        $btn = '<a class="mr-1" href="/php-laravel/tutor/' . "tutors_exit_evaluation/add/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="view">Add</a>';
                    }
                    return $btn;
                })
                ->addColumn('action', function ($row) {

                    $edit = '<a href="/php-laravel/tutor/' . "edit/" . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="Edit"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>   '
                        . '<a  id="deleteUser"  data-id="' . encrypt($row->tutor_id) . '"  data-model="Tutor" data-toggle="tooltip" data-placement="top" title="Delete"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></i></a>';
                    return $edit;
                })
                ->addColumn('view', function ($row) {
                    // \Log::info($row);
                    $view = '<a href="admin/student/' . encrypt($row->tutor_id) . '" data-toggle="tooltip" data-placement="top" title="View"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></i></a>';
                    return $view;
                })

                ->filter(function ($instance) use ($request) {

                    if ($request->get('site_coordinator')) {
                        $instance->where('university_name', $request->get('site_coordinator'));
                    }

                    if ($request->get('teacher')) {
                        $instance->where('teacher_user.name',  $request->get('teacher'));
                    }

                    if ($request->get('school')) {
                        $instance->where('school_name',  $request->get('school'));
                    }

                    if ($request->get('status')) {
                        $instance->where('tutor_user.status',  $request->get('status'));
                    }

                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('teacher_user.name', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['action', 'view', 'weekly_progress', 'study_group', 'entry_evaluation', 'exit_survey', 'exit_evaluation'])
                ->make(true);
        }
        $siteCoordinators = User::where('user_type', 'SC')->get(['name']);
        $teachers         = User::where('user_type', 'T')->get(['name']);
        $schools          = School::select('*')->get(['school_name']);
        $userType         = $user->user_type;
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => "javascript:void(0)", 'name' => "Add Tutor"],
        ];
        return view('Tutor.list', compact('siteCoordinators', 'teachers', 'schools', 'breadcrumbs', 'userType'));
    }
}
