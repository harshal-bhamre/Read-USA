<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\sitecoordinator;
use App\Models\user;
use App\Models\Detail;
use App\Http\Controllers\Details;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Exports\SitecoordinatorExport;
use Maatwebsite\Excel\Facades\Excel;

class sitecoordinatorcontroller extends Controller
{

    // index method
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = sitecoordinator::select('sitecoordinator.*', 'users.*')
                ->join('users', 'sitecoordinator.user_id', '=', 'users.id');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('SchoolRegistrationLink', function ($row) {
                    $view = '<a href="teacher/add">Link</a>';
                    return $view;
                })
                ->addColumn('TeacherRegistrationLink', function ($row) {
                    $view = '<a href="admin/school/add">Link</a>';
                    return $view;
                })
                ->addColumn('email', function ($row) {
                    return '<a href=mailto:' . $row->email . '>' . $row->email . '</a>';
                })
                ->addColumn('action', function ($row) {
                    $editLink = '<a href="/php-laravel/siteCoordinator/edit/' . encrypt($row->university_id) . '" data-toggle="tooltip" data-placement="top" title="Edit"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>';
                    $deleteLink = '<a id="deleteUser" class="text-primary ml-1" data-id="' . encrypt($row->university_id) . '" data-model="SiteCoordinator" data-toggle="tooltip" data-placement="top" title="Delete"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></i></a>';
                    return $editLink . $deleteLink;
                })
                ->addColumn('ViwesSchool', function ($row) {
                    $view = '<a class="mr-1" href="/php-laravel/app/' . "schools/" . encrypt($row->university_id) . '" data-toggle="tooltip" data-placement="top" title="view"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></i></a>';
                    return $view;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == 'inactive' || $request->get('status') == 'active') {
                        $instance->where('status', $request->get('status'));
                    }
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('university_name', 'LIKE', "%$search%");
                        });
                    }
                })
                ->rawColumns(['action', 'ViwesSchool', 'SchoolRegistrationLink', 'TeacherRegistrationLink', 'email'])
                ->make(true);
        }
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => "javascript:void(0)", 'name' => "SiteController"],
        ];
        return view('SiteCoordinator/list', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    // create method
    public function create()
    {
        return view("siteCoordinator/create");
    }

    // store method 
    public function store(Request $request)
    {
        $rules = [
            'university_name'    => 'required|string|max:255',
            'email'              => 'required|string|email|max:255',
            'address'            => 'required|string|max:255',
            'phone'              => 'required|min:10',
            'password'           => 'required|required|min:8|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).*$/',
            'district'           => 'required',
            'building'           => 'required',
            'doa'                => 'required',
            'gender'             => 'required',
            'trained'            => 'required',
            'primary_role'       => 'required',
            'experience'         => 'required',
            'highest_edu'        => 'required',
        ];

        $data = Validator::make($request->all(), $rules);
        if ($data->fails()) {
            return redirect()->back()->withErrors($data->errors())->withInput();
        }

        $data = $request->all();
        $data["password"] = Hash::make($data["password"]);
        $data["role"] = "S";
        $data["status"] = "active";
        $data["name"] = request("university_name");
        $data["user_type"] = "SC";
        $user = User::create($data);

        $data["race"] = implode(",", $data["race"]);
        $details = Detail::create($data);

        $data['user_id'] = $user->id;
        $data['details_id'] = $details->details_id;
        $data['enrollment'] = "Approved";
        SiteCoordinator::create($data);

        return redirect(url("siteCoordinator/list"))->with("success", "SiteCoordinator Edited Successfully");
    }

    // edit method 
    public function edit($id)
    {
        $id = decrypt($id);
        $data = SiteCoordinator::select('*')
            ->join("users", "users.id", "=", "sitecoordinator.user_id")
            ->join("details", "details.details_id", "=", "sitecoordinator.details_id")->where("sitecoordinator.university_id", $id)->get();
        return view('siteCoordinator/edit', compact('data'));
    }

    // update method 
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'university_name'    => 'required|string|max:255',
            'email'              => 'required|string|email|max:255',
            'address'            => 'required|string|max:255',
            'phone'              => 'required|min:10',
            'district'           => 'required',
            'building'           => 'required',
            'doa'                => 'required',
            'gender'             => 'required',
            'primary_role'       => 'required',
            'experience'         => 'required',
            'highest_edu'        => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();

        $inputSc["university_id"] = $request->university_id;
        $siteCoordinator = SiteCoordinator::updateOrCreate($inputSc, $input);

        $inputUser['id'] = $siteCoordinator->user_id;
        $searchInputDetails['details_id'] = $siteCoordinator->details_id;
        $input["name"] = request("university_name");
        $user = User::updateOrCreate($inputUser, $input);

        $details = Detail::updateOrCreate($searchInputDetails, $input);

        if ($user && $details && $siteCoordinator) {
            return redirect(url("siteCoordinator/list"))->with("success", "Site Coordinator Edited Successfully");
        } else {
            return redirect(url("siteCoordinator/list"))->with("error", "Something went wrong");
        }
    }

    // export method 
    public function export()
    {
        return Excel::download(new SitecoordinatorExport, 'sitecoordinator.xlsx');
    }
}
