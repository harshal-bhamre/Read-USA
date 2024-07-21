<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Sitecoordinator;
use App\Models\School;
use App\Models\User;
use App\Models\Cohort;
use App\Models\Teacher;
use App\Models\Tutor;
use App\models\Studententryevalution;
use App\models\Studentmidevalution;
use App\models\Studentexitevalution;
use App\models\Studentexitsurvey;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentExport;
use App\Exports\StudentEntryEvalutionExport;
use App\Exports\StudentEntrySurveyExport;
use App\Exports\StudentMidEvalutionExport;
use App\Exports\StudentExitEvalutionExport;
use App\Exports\StudentExitSurveyExport;
use App\Exports\StudentWeeklyProgressExport;
use App\Exports\StudentWordIdentificationExport;
use App\Exports\StudentBookExport;
use App\Models\ExitSurvey;
use App\Models\StudentEntryEvaluation;
use App\Models\StudentExitEvaluation;
use App\Models\StudentMidEvaluation;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    public function index(Request $request, $id = null)
    {
        if ($request->ajax()) {
            $data = DB::table('students')
                ->select(
                    '*',
                    'teacher_users.name as teacher_name',
                    'tutor_users.name as tutor_name',
                    'students.status as status'
                )
                ->leftJoin('sitecoordinator', 'sitecoordinator.university_id', '=', 'students.university_id',)
                ->leftJoin('schools', 'schools.school_id', '=', 'students.school_id')
                ->leftJoin('teachers', 'teachers.teacher_id', '=', 'students.teacher_id')
                ->join('users as teacher_users', 'teacher_users.id', '=', 'teachers.user_id')
                ->leftJoin('tutors', 'tutors.tutor_id', '=', 'students.tutor_id')
                ->join('users as tutor_users', 'tutor_users.id', '=', 'tutors.user_id')
                ->leftJoin('cohorts', 'cohorts.cohort_id', '=', 'students.cohort_id');


            if ($request->site_coordinator_id != null) {
                $data = $data->where('sitecoordinator.university_id', $request->site_coordinator_id);
            }

            if ($request->teacherId != null) {
                $data = $data->where('teachers.teacher_id', $request->teacherId);
            }

            if ($request->schoolId != null) {
                $data = $data->where('schools.school_id', $request->schoolId);
            }

            if ($request->tutorId != null) {
                $data = $data->where('tutors.tutor_id', $request->tutorId);
            }

            if ($request->cohortId != null) {
                $data = $data->where('cohorts.cohort_id', $request->cohortId);
            }

            if ($request->status != '') {
                $data->where('students.status', $request->status);
            }

            if ($id) {
                $id = decrypt($id);
                $data->where("tutors.tutor_id", $id);
            }

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('weekly_progress', function ($row) {
                return '<div class="d-flex align-items-center col-actions">' .
                '<a class="mr-1 btn btn-primary" href="'."student/" . "addWeeklyProgress/" . encrypt($row->student_id) . '" data-toggle="tooltip" data-placement="top" title="Add">Add</a>' .
                '</div>';
            })
            ->addColumn('entry_survey', function ($row) {
                return '<div class="d-flex align-items-center col-actions">' .
                '<a class="mr-1" href="'."student/" . "entry_survey/" . encrypt($row->student_id) . '" data-toggle="tooltip" data-placement="top" title="">'.substr($row->created_at, 0, 10).'</a>' .
                '</div>';
            })
            ->addColumn('exit_survey', function ($row) {
                $exitSurvey = ExitSurvey::where('student_id', $row->student_id)->first();
                if($exitSurvey){
                    return '<div class="d-flex align-items-center col-actions">' .
                    '<a class="mr-1" href="'."student/" . "edit_exit_survey/" . encrypt($row->student_id) . '" data-toggle="tooltip" data-placement="top" title="Edit"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>' .
                    '<a class="mr-1" href="' . "/delete/" . encrypt($row->student_id) . '" data-toggle="tooltip" data-placement="top" title="Delete"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></i></a>' .
                    '</div>';
                }else{
                    return '<div class="d-flex align-items-center col-actions">' .
                    '<a class="mr-1 btn btn-primary" href="'."student/" . "add_exit_survey/" . encrypt($row->student_id) . '" data-toggle="tooltip" data-placement="top" title="Add">Add</a>' .
                    '</div>';
                }
            })
            ->addColumn('entry_evaluation', function ($row) {
                $entryEvaluation = StudentEntryEvaluation::where('student_id', $row->student_id)->first();
                if($entryEvaluation){
                    return '<div class="d-flex align-items-center col-actions">' .
                    '<a class="mr-1" href="'."student/" . "edit_entry_evaluation/" . encrypt($row->student_id) . '" data-toggle="tooltip" data-placement="top" title="Edit"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>' .
                    '</div>';
                }else{
                    return '<div class="d-flex align-items-center col-actions">' .
                    '<a class="mr-1 btn btn-primary" href="'."student/" . "add_entry_evaluation/" . encrypt($row->student_id) . '" data-toggle="tooltip" data-placement="top" title="Add">Add</a>' .
                    '</div>';
                }
            })
            ->addColumn('mid_evaluation', function ($row) {
                $midEvaluation = StudentMidEvaluation::where('student_id', $row->student_id)->first();
                if($midEvaluation){
                    return '<div class="d-flex align-items-center col-actions">' .
                    '<a class="mr-1" href="'."student/" . "edit_mid_evaluation/" . encrypt($row->student_id) . '" data-toggle="tooltip" data-placement="top" title="Edit"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>' .
                    '</div>';
                }else{
                    return '<div class="d-flex align-items-center col-actions">' .
                    '<a class="mr-1 btn btn-primary" href="'."student/" . "add_mid_evaluation/" . encrypt($row->student_id) . '" data-toggle="tooltip" data-placement="top" title="Add">Add</a>' .
                    '</div>';
                }
            })
            ->addColumn('exit_evaluation', function ($row) {
                $exitEvaluation = StudentExitEvaluation::where('student_id', $row->student_id)->first();
                if($exitEvaluation){
                    return '<div class="d-flex align-items-center col-actions">' .
                    '<a class="mr-1" href="'."student/" . "edit_exit_evaluation/" . encrypt($row->student_id) . '" data-toggle="tooltip" data-placement="top" title="Edit"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>' .
                    '</div>';
                }else{
                    return '<div class="d-flex align-items-center col-actions">' .
                    '<a class="mr-1 btn btn-primary" href="'."student/" . "add_exit_evaluation/" . encrypt($row->student_id) . '" data-toggle="tooltip" data-placement="top" title="Add">Add</a>' .
                    '</div>';
                }
            })
            ->addColumn('Action', function ($row) {
                return '<div class="d-flex align-items-center col-actions">' .
                '<a class="mr-1 " href="'."student/" . "edit/" . encrypt($row->student_id) . '" data-toggle="tooltip" data-placement="top" title="Edit"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></i></a>' .
                '<a class="mr-1 " href="' . "/delete/" . encrypt($row->student_id) . '" data-toggle="tooltip" data-placement="top" title="Delete"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></i></a>' .
                '</div>';
            })
            ->addColumn('view_progress_report', function ($row) {
                return '<div class="d-flex align-items-center col-actions">' .
                '<a class="mr-1" href="' . "student/view/" . encrypt($row->student_id) . '" data-toggle="tooltip" data-placement="top" title="View"><i><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></i></a>' .
                '</div>';
            })
            ->addColumn('study_group_type', function ($row) {
                return 'ReadUSA';
            })
            // ->filter(function ($instance) use ($request) {
            
            //     if (!empty($request->search)) {
            //         $search = $request->search;
            //         $instance->where(function($q) use ($search) {
            //             $q->where('students.name', 'like', "%$search%")
            //             ->orWhere('students.email', 'like', "%$search%")
            //             ->orWhere('sitecoordinator.university_name', 'like', "%$search%")
            //             ->orWhere('schools.school_name', 'like', "%$search%")
            //             ->orWhere('teachers.name', 'like', "%$search%")
            //             ->orWhere('tutors.name', 'like', "%$search%")
            //             ->orWhere('cohorts.cohort_name', 'like', "%$search%");
            //         });
            //     }
            // })
            ->rawColumns(['Action', 'weekly_progress', 'exit_survey', 'entry_survey','entry_evaluation', 'mid_evaluation', 'exit_evaluation', 'view_progress_report', 'study_group_type'])
            ->make(true);
                
        }
        $siteCoordinators = sitecoordinator::pluck("university_name", "university_id");
        $teachers = Teacher::join('users as teacher_users', 'teacher_users.id', '=', 'teachers.user_id')->pluck('name', 'teacher_id');
        $schools = School::pluck('school_name', 'school_id');
        $tutor = Tutor::join('users as tutor_users', 'tutor_users.id', '=', 'tutors.user_id')->pluck('name', 'tutor_id');
        $cohort = Cohort::pluck('cohort_name', 'cohort_id');
        return view("student.index", compact('siteCoordinators', 'teachers', 'schools', 'tutor', 'cohort'));
    }

    public function create(){
        $cohort = Cohort::pluck('cohort_name', 'cohort_id');
        $siteCo = Sitecoordinator::pluck('university_name', 'university_id');
        return view("student.create", compact('cohort', 'siteCo'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'cohort_id' => 'required',
            'university_id' => 'required',
            'school_id' => 'required',
            'teacher_id' => 'required',
            'tutor_id' => 'required',
            'student_name' => 'required | max:50',
            'dcps_id' => 'required',
            'start_date' => 'required',
            'wave' => 'required'
        ]);

        $studentInput = $request->all();
        // dd($studentInput);
        $studentInput['race'] = implode(',', $studentInput['race']);
        $studentInput['cohort_id'] = implode(',', $studentInput['cohort_id']);
        $studentInput['status'] = 'active';
        Student::create($studentInput);
        return redirect('app/student/');
    }

    public function edit($id)
    {
        $studentId = decrypt($id);
        $cohort = Cohort::pluck('cohort_name', 'cohort_id');
        $siteCo = sitecoordinator::pluck('university_name', 'university_id');
        $students = Student::find($studentId);
        return view('student.edit', compact('students', 'cohort', 'siteCo'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'cohort_id' => 'required',
            'university_id' => 'required',
            'school_id' => 'required',
            'teacher_id' => 'required',
            'tutor_id' => 'required',
            'student_name' => 'required | max:50',
            'dcps_id' => 'required',
            'start_date' => 'required',
            'wave' => 'required',
        ]);
        $studentInput = $request->all();
        // dd($studentInput);
        $studentId = $studentInput['student_id'];

        $raceArray = $request->input('race');
        $raceArray = array_filter($raceArray);
        $studentInput['race'] = implode(', ', $raceArray);

        $studentInput['cohort_id'] = implode(', ', $studentInput['cohort_id']);
        $id = ["student_id" => $studentId];
        Student::updateOrCreate($id, $studentInput);

        return redirect('app/student/');
    }

    public function entrySurvey($id)
    {
        $studentId = decrypt($id);
        $student = Student::find($studentId);
        // dd($student);
        return view('student/entry_survey', compact('student'));
    }

    public function export(Request $request)
    {
        // dd($request->all());
        $data = $request->all();
        switch ($data["form-select"]) {
            case '1':
                return Excel::download(new StudentExport($data["select-cohort"]), 'Active_student.xlsx');

            case '2':
                // return Excel::download(new StudentBookExport($data["select-cohort"]), 'BookLevel.xlsx');

            case 3:
                // return Excel::download(new StudentEntryEvalutionExport($data["select-cohort"]), 'StudentEntryEvalution.xlsx');

            case 4:
                // return Excel::download(new StudentEntrySurveyExport($data["select-cohort"]), 'StudentEntrySurvey.xlsx');

            case 5:
                // return Excel::download(new StudentMidEvalutionExport($data["select-cohort"]), 'StudentMidEvalutions.xlsx');

            case 6:
                // return Excel::download(new StudentExitEvalutionExport($data["select-cohort"]), 'StudentExitEvalutions.xlsx');


            case 7:
                // return Excel::download(new StudentExitSurveyExport($data["select-cohort"]), 'StudentExitSurvey.xlsx');

            case 8:
                // return Excel::download(new StudentWeeklyProgressExport($data["select-cohort"]), 'StudentWeeklyProgress.xlsx');

            case 9:
                // return Excel::download(new StudentWordIdentificationExport($data["select-cohort"]), 'StudentWordIdentificationExport.xlsx');
        }
    }

}
