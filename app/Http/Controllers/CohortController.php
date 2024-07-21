<?php

namespace App\Http\Controllers;

use App\Models\Cohort;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class CohortController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Cohort::select('*');
            // \Log::info($data);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $edit = '<a href="/php-laravel/' . "cohort/edit/" . encrypt($row->cohort_id) . '" data-toggle="tooltip" data-placement="top" title="Edit"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>   '
                        // "/php-laravel/tutor' . "/tutors_evaluation/edit/" . encrypt($row->tutor_id) . '"      
                        . '<a  id="deleteUser" class="text-primary ml-1" data-id="' . encrypt($row->cohort_id) . '" data-model="Cohort" data-toggle="tooltip" data-placement="top" title="Delete"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></i></a>';
                    return $edit;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => "javascript:void(0)", 'name' => "Cohort"],
        ];
        return view('cohort.list', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function create()
    {
        $breadcrumbs = [
            ['link' => "/", 'name' => "Home"],
            ['link' => "javascript:void(0)", 'name' => "Add Cohort"],
        ];
        return view('cohort.add', [
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cohort_name'       => 'required',
            'start_date'        => 'required',
            'end_date'          => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $input = $request->all();
        Cohort::create($input);
        return redirect(url("cohort/list"))->with("success", "SiteCoordinator Edited Successfully");
    }

    public function edit($id)
    {
        // dd($id);
        $id = decrypt($id);
        $data = Cohort::find($id);
        return view('cohort.edit', compact('data'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cohort_name'       => 'required',
            'start_date'        => 'required',
            'end_date'          => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $input = $request->all();
        $condition['cohort_id'] = $input['cohort_id'];
        $cohort = Cohort::updateOrCreate($condition, $input);
        if ($cohort) {
            return redirect(url("cohort/list"))->with("success", "SiteCoordinator Edited Successfully");
        } else {
            return redirect(url("cohor/list"))->with("error", "something went wrong");
        }
    }
}
