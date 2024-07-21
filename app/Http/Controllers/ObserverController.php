<?php

namespace App\Http\Controllers;

use App\Models\Observer;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class ObserverController extends Controller
{
    //Listing
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Observer::select("*")->join("users", "users.id", "=", "observers.user_id")
            ->leftJoin("schools", "schools.school_id", "=", "observers.school_id")
            ->where('users.user_type', '=', 'O')
            ->get()
            ->map(function ($observer) {
                $observer->role = $observer->school_id ? 'School Principal' : 'District Level Observer';
                return $observer;
            });
            
            return DataTables::of($data)->addIndexColumn()
                ->addColumn('Action', function ($row) {
                    return '<div class="d-flex align-items-center col-actions">' .
                        '<a class="mr-1" href="'."observer/"."edit/" . encrypt($row->observer_id) . '" data-toggle="tooltip" data-placement="top" title="Edit"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></i> </a>'.
                        '<a class="text-primary mr-1"  id="deleteUser"  data-id="' . encrypt($row->observer_id) . '"  data-model="Observer" data-toggle="tooltip" data-placement="top" title="Delete"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></i></a>'.
                        '<a class="mr-1" href="' . "observer/view/" . encrypt($row->observer_id) . '" data-toggle="tooltip" data-placement="top" title="View"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></i></a>' .
                        '</div>';
                })->addColumn("created_at", function ($row) {
                    return $row->created_at;
                })
                ->rawColumns(['Action'])
                ->make(true);
        }

        return view("observer.index");
    }

    //Create
    public function create()
    {
        $schools = School::pluck("school_name","school_id");
        return view('observer/create', compact('schools'));
    }

    //Store
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'phone' => 'required|max:10',
                'status' => 'required',
                
            ]);
            if ($request->role == 'SP'){
                $request->validate([
                    'school_name' => 'required',
                ]);
            }


        $input = $request->all();
        
        $input['password'] = Hash::make($input['password']);
        $input['user_type'] = 'O';
        // dd($input);
        $user = User::create($input);
        Observer::create([
            'user_id' => $user->id,
            'school_id' => $request->school_name,
        ]);
        return redirect('app/observer/');
    }

    //Edit
    public function edit($id)
    {
        $observerid = decrypt($id);
        $observer = Observer::select("*")->join("users", "users.id", "observers.user_id")->where("observer_id", $observerid)->get();
        $schools = School::pluck("school_name","school_id");
        return view('observer.edit', compact('observer', 'schools'));
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'role' => 'required',
            'name' => 'required | max:50',
            'phone' => 'required|max:10',
            'status' => 'required',
        ]);
        if ($request->role == 'SP'){
            $request->validate([
                'school_id' => 'required',
            ]);
        }
        
    $input = $request->all();
    if($request->role == 'DO'){
        $input['school_id'] = null;
    }
    $searchInput["observer_id"] =$request["observer_id"];
    $var = Observer::updateOrCreate($searchInput, $input);
    
    $searchInputUser["id"] = $var->user_id;
    User::updateOrCreate($searchInputUser, $input);
    return redirect('app/observer/'); 
    }

    public function view($id){
       $observerId = decrypt($id);
       $observer = Observer::select("*")->join("users", "users.id", "observers.user_id")->where("observer_id", $observerId)->get();
       $schools = School::pluck("school_name","school_id");
       return view('observer.view', compact('observer', 'schools'));
    }
}
