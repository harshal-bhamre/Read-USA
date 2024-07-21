<?php

namespace App\Exports;

use App\Models\TutorExitSurvey;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TutorExitSurveyExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // return TutorExitSurvey::all();
        return TutorExitSurvey::select(
            "*",
            "tutor_user.name as tutor_name",
            "tutor_user.email as tutor_email",
            "sitecoordinator.university_name as sitecoordinators_name",
            "teacher_user.name as teacher_name",
            // "tutors_weekly_progress.created_at as create_date",
            "school_name",
            // "tutors_weekly_progress.updated_at as updated_date"
        )
            ->leftJoin("tutors", "tutors.tutor_id", "=", "tutor_exit_surveys.tutor_id")
            ->join("users as tutor_user", "tutor_user.id", "=", "tutors.user_id", "left")
            
            ->join("teachers", "teachers.teacher_id", "=", "tutors.teacher_id", "left")
            ->join("users as teacher_user", "teacher_user.id", "=", "teachers.user_id", "left")
            ->leftJoin("schools", "schools.school_id", "=", "teachers.school_id")
            // ->join("tutor_exit_surveys", "tutor_exit_surveys.tutor_id", "=", "tutors.tutor_id")
            ->join("sitecoordinator", "sitecoordinator.university_id","=","schools.university_id","left")
            ->join("details", "details.details_id", "=", "tutors.details_id", "left")

            ->get();
    }

    public function headings(): array
    {
        return [
            "SNo",
            "Sitecoordinators Name",
            "Teacher Name",
            "School Name",
            "Tutor Name",
            "Email",
            "Status",
            "Last Lesson",
            "ReadUsa Lesson",
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
            $w->sitecoordinators_name,
            $w->teacher_name,
            $w->school_name,
            $w->tutor_name,
            $w->tutor_email,
            $w->status,
            $w->last_lesson,
            $w->readusa_lesson,
            $w->created_at,
            $w->updated_at,
        ];
    }
}
