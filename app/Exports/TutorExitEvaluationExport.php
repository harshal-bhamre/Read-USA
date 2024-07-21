<?php

namespace App\Exports;

use App\Models\TutorExitEvaluation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TutorExitEvaluationExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // return TutorExitEvaluation::all();
        return TutorExitEvaluation::select('*', "tutor_user.name as tutor_name", "tutor_user.email as tutor_email", "sitecoordinator.university_name as sitecoordinators_name", "school_name", "teacher_user.name as teacher_name", "tutors.created_at as create_date", "tutors.updated_at as updated_date")
            ->leftJoin("tutors", "tutors.tutor_id", "=", "tutor_exit_evaluations.tutor_id")
            ->join("users as tutor_user", "tutor_user.id", "=", "tutors.user_id", "left")
            ->join("teachers", "teachers.teacher_id", "=", "tutors.tutor_id", "left")
            ->join("users as teacher_user", "teacher_user.id", "=", "teachers.user_id", "left")

            ->join("schools", "schools.school_id", "=", "teachers.school_id", "left")
            ->join("sitecoordinator", "sitecoordinator.university_id", "=", "schools.university_id", "left")
            ->get();
    }

    public function headings(): array
    {
        return [
            "SNo",
            "Tutor Name",
            "Email",
            "Sitecoordinators Name",
            "Teacher Name",
            "School Name",
            "Gort Assessment",
            "When was started",
            "Rate Row Score",
            "Accuracy Raw Score",
            "Fluency Raw Score",
            "Comprehension Raw Score",
            "Create_date",
            "Updated_date",

        ];
    }
    public function map($w): array
    {
        static $count = 0;
        $count++;

        return [
            $count,
            $w->tutor_name,
            $w->tutor_email,
            $w->sitecoordinators_name,
            $w->teacher_name,
            $w->school_name,
            $w->gort,
            $w->gort_assessment,
            $w->rate_row_score,
            $w->accuracy_raw_score,
            $w->fluency_raw_score,
            $w->comprehension_raw_score,
            $w->create_date,
            $w->updated_date,
        ];
    }
}
