<?php

namespace App\Exports;

use App\Models\TutorWeeklyProgress;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TutorWeeklyProgressExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // return TutorWeeklyProgress::all();
        return TutorWeeklyProgress::select(
            "*",
            "tutor_user.name as tutor_name",
            "tutor_user.email as tutor_email",
            "sitecoordinator.university_name as sitecoordinators_name",
            "teacher_user.name as teacher_name",
            "school_name",
        )
            ->leftJoin("tutors", "tutors.tutor_id", "=", "tutor_weekly_progress.tutor_id")
            ->join("users as tutor_user", "tutor_user.id", "=", "tutors.user_id", "left")
            ->join("teachers", "teachers.teacher_id", "=", "tutors.teacher_id", "left")
            ->join("users as teacher_user", "teacher_user.id", "=", "teachers.user_id", "left")
            ->leftJoin("schools", "schools.school_id", "=", "teachers.school_id")
            ->join("sitecoordinator", "sitecoordinator.university_id", "=", "schools.university_id", "left")
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
            "Weekly",
            "In the last 3 days(T,W,Th) how many Read USA lesson",
            "Monday Attendence",
            "Tuesday Attendece",
            "Wednesday Attendence",
            "Thursday Attendence",
            "Friday Attendence",
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
            $w->lessons,
            $w->attendence_monday,
            $w->attendence_Tuesday,
            $w->attendence_wednesday,
            $w->attendence_thursday,
            $w->attendence_friday,
            $w->create_date,
            $w->updated_date,
        ];
    }
}
