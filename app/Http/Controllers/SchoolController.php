<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\sitecoordinator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class SchoolController extends Controller
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
        if ($request->ajax()) {
            $data = School::select("*")
                ->join("sitecoordinator", "sitecoordinator.university_id", "=", "schools.university_id");

            if ($request->site_coordinator_id != null) {
                $data = $data->where('sitecoordinator.university_id', $request->site_coordinator_id);
            }
            if ($request->status != '') {
                $data->where('schools.status', $request->status);
            }
            if ($id) {
                $data->where("sitecoordinator.university_id", decrypt($id));
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('Action', function ($row) {
                    return '<div class="d-flex align-items-center col-actions">' .
                        '<a class="mr-1" href="' . "schools/" . "edit/" . encrypt($row->school_id) . '" data-toggle="tooltip" data-placement="top" title="Edit"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>' .
                        '<a class="text-primary mr-1"  id="deleteUser"  data-id="' . encrypt($row->school_id) . '"  data-model="School" data-toggle="tooltip" data-placement="top" title="Delete"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></i></a>' .
                        '</div>';
                })
                ->addColumn('ViewTeacher', function ($row) use ($user) {
                    if ($user->user_type == 'A') {
                        $btn =  '<div class="d-flex align-items-center col-actions">' .
                            '<a class="mr-1" href="/php-laravel/teacher/list/' . encrypt($row->school_id) . '" data-toggle="tooltip" data-placement="top" title="View"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></i></a>' .
                            '</div>';
                    } else if ($user->user_type == 'O') {
                        $btn =  '<div class="d-flex align-items-center col-actions">' .
                            '<a class="mr-1" href="/php-laravel/observer/teachers/list/' . encrypt($row->school_id) . '" data-toggle="tooltip" data-placement="top" title="View"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></i></a>' .
                            '</div>';
                    }
                    return $btn;
                })
                ->addColumn('teacher_registration_link', function ($row) {
                    return '<div class="d-flex align-items-center col-actions">' .
                        '<a class="mr-1" href="' . "php-laravel/addteachers/" . encrypt($row->school_id) . '" data-toggle="tooltip" data-placement="top" title="">Link</a>' .
                        '<a class="mr-1 btn btn-primary" href="' . "teacher/view/" . encrypt($row->school_id) . '" data-toggle="tooltip" data-placement="top" title="">Send</a>' .
                        '</div>';
                })->addColumn("created_at", function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->filter(function ($instance) use ($request) {

                    if (!empty($request->get('search'))) {

                        $instance->where(function ($instances) use ($request) {
                            $search = $request->get('search');
                            $instances->orWhere('university_name', 'LIKE', "%$search%");
                            $instances->orWhere('school_name', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['Action', 'ViewTeacher', 'teacher_registration_link'])
                ->make(true);
        }

        $siteCoordinators = sitecoordinator::pluck("university_name", "university_id");
        $userType         = $user->user_type;
        return view("school.index", compact('siteCoordinators', 'userType'));
    }

    public function create()
    {
        $siteCo = sitecoordinator::pluck("university_name", "university_id");
        return view("school/create", compact('siteCo'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // request()->validate([
        //     "university_id"=> "required",
        //     "school_name"=> "required",
        //     "tutoring_date"=> "required",
        // ]);
        $input = $request->all();
        School::create($input);
        return redirect('app/schools/');
    }

    public function edit($id)
    {
        $schoolId = decrypt($id);
        $schools = School::find($schoolId);
        return view('school.edit', compact('schools'));
    }

    public function update(Request $request)
    {
        //   $request->validate([
        //     'school_name'=> 'required',
        //     'tutoring_date'=> 'required',
        //     'status'=> 'required',
        //   ]);
        $input = $request->all();
        //   dd($input);
        $searchInput['school_id'] = $request['school_id'];
        School::updateOrCreate($searchInput, $input);
        return redirect('app/schools/');
    }

    public function fetchSchools($universityId)
    {
        $schools = School::where('university_id', $universityId)->pluck('school_name', 'school_id');
        // log::info($schools);
        return response()->json($schools);
    }
}
