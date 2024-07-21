<?php


namespace App\Http\Controllers;

use App\Exports\TeacherExport;
use App\Models\sitecoordinator;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\user;
use App\Models\Detail;
use App\Models\School;
use App\Http\Controllers\Details;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class TeachersController extends Controller
{

    // teacher setion 
    public function index(Request $request, $id = null)
    {
        if ($request->ajax()) {
            $data = Teacher::select('*')
                ->join("users", "users.id", "=", "teachers.user_id", "left")
                ->join("schools", "schools.school_id", "=", "teachers.school_id", "left")
                ->join("details", "details.details_id", "=", "teachers.details_id", "left")
                ->join("sitecoordinator", "sitecoordinator.university_id", "=", "schools.university_id");

            if ($id) {
                $data->where("teachers.school_id", decrypt($id));
            }

            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('TutorRegistrationLink', function () {
                    $view = '<a href="/php-laravel/tutor/' . "add/" . '" class="btn btn-danger">Link</a>';
                    return $view;
                })

                ->addColumn('email', function ($row) {
                    return '<a href=mailto:' . $row->email . '>' . $row->email . '</a>';
                })

                ->addColumn('action', function ($row) {
                    $edit = '<a href="' . "edit/" . encrypt($row->teacher_id) . '" data-toggle="tooltip" data-placement="top" title="Edit"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>'
                        . '<a id="deleteUser" class="text-primary ml-1" data-id="' . encrypt($row->teacher_id) . '"  data-model="Teacher" data-toggle="tooltip" data-placement="top" title="Delete"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></i></a>';
                    return $edit;
                })

                ->addColumn('view', function ($row) {
                    $view = '<a href="/php-laravel/tutor/' . "list/" . encrypt($row->teacher_id) . '" data-toggle="tooltip" data-placement="top" title="View"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></i></a>';
                    return $view;
                })

                ->addColumn("created_at", function ($row) {
                    return $row->created_at->toDateTimeString();;
                })

                ->filter(function ($instance) use ($request) {

                    if ($request->get('site_coordinator')) {
                        $instance->where('university_name', $request->get('site_coordinator'));
                    }

                    if ($request->get('school')) {
                        $instance->where('school_name',  $request->get('school'));
                    }

                    if ($request->get('status')) {
                        $instance->where('users.status',  $request->get('status'));
                    }

                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('name', 'LIKE', "%$search%");
                        });
                    }
                })

                ->rawColumns(['TutorRegistrationLink', 'action', 'view', 'email', 'created_at'])
                ->make(true);
        }
        $siteCoordinators       = User::where('user_type', 'SC')->get();
        $schools                = School::select('*')->get();
        return view('Teachers.list', compact('siteCoordinators', 'schools'));
    }

    // add teacher 
    public function create($id = null)
    {
        $siteCoordinators = SiteCoordinator::all();
        // if ($id) {
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => "javascript:void(0)", 'name' => "Add Teacher"],
        ];
        return view('Teachers.add', compact('siteCoordinators', 'breadcrumbs'));
        // }
    }

    // get school dynamically in form 
    public function getSchoolsByUniversity(Request $request)
    {
        if ($request->ajax()) {
            Log::info($request->all());

            $universityId = $request->universityId;
            $schools = School::where('university_id', $universityId)->get();
            return response()->json($schools);
        }
    }

    // store school 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'              => 'required|string',
            'school_id'         => 'required',
            'university_id'     => 'required',
            'email'             => 'required|string|email',
            'address'           => 'required|string',
            'phone'             => 'required|string',
            'district'          => 'required',
            'building'          => 'required',
            'doa'               => 'required',
            'gender'            => 'required',
            'hispanic'          => 'required',
            'trained'           => 'required',
            'primary_role'      => 'required',
            'experience'        => 'required',
            'highest_edu'       => 'required',
        ]);
        if ($validator->fails()) {
            // dd("fails");
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['user_type'] = 'T';
        $input['status'] = 'active';
        $user = User::create($input);
        if ($request->has("race")) {
            $input["race"] = implode(",", $input["race"]);
        }
        $details = Detail::create($input);

        $input['user_id'] = $user->id;
        $input['details_id'] = $details->details_id;
        Teacher::create($input);

        return redirect(url("/teacher/list"))->with("success", "Added successfully Successfully");
    }

    // export teacher list 
    public function export()
    {
        return Excel::download(new TeacherExport, 'Teacher.xlsx');
    }

    // edit teacher 
    public function edit($id)
    {
        $id = decrypt($id);
        $data = Teacher::find($id);
        $data = Teacher::select('*')
            ->join("users", "users.id", "=", "teachers.user_id")
            ->join("details", "details.details_id", "=", "teachers.details_id")->where("teachers.teacher_id", $id);
        $data = $data->first()->toArray();
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => "javascript:void(0)", 'name' => "Edit Teacher"],
        ];
        return view('Teachers.edit', compact('data', 'breadcrumbs'));
    }

    // update teacher 
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email',
            'address' => 'required|string',
            'phone' => 'required|string',
            'district' => 'required',
            'building' => 'required',
            'doa' => 'required',
            'gender' => 'required',
            'trained' => 'required',
            'primary_role' => 'required',
            'experience' => 'required',
            'highest_edu' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $inp = $request->all();
        $inp["race"] = join(",", $inp["race"]);
        $searchInput["teacher_id"] = $inp["teacher_id"];
        DB::beginTransaction();
        try {
            $tutor = Teacher::updateOrCreate($searchInput, $inp);

            $searchDetails['details_id'] = $tutor->details_id;
            $details = Detail::updateOrCreate($searchDetails, $inp);

            $searchUser['id'] = $tutor->user_id;
            $user = User::updateOrCreate($searchUser, $inp);

            if ($tutor &&  $details &&  $user) {
                DB::commit();
                return redirect(url("teacher/list"))->with("success", "Student added successfully ");
            } else {
                dd($searchInput);
                return redirect()->back()->with("error", "Something went wrong");
            }
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with("error", "Something went wrong");
        }
    }
}
