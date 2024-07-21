<?php

namespace App\Exports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TeacherExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Teacher::select('*')
            ->join("users", "users.id", "=", "teachers.user_id", "left")
            ->join("details", "details.details_id", "=", "teachers.details_id", "left")
            ->get();
    }

    public function map($w): array
    {
        static $count = 0;
        $count++;

        return [

            $count,
            $w->name,
            $w->email,
            $w->phone,
            $w->status,
            $w->created_at,
            $w->district,
            $w->building,
            $w->address,
            $w->doa,
            $w->gender,
            $w->hispanic,
            $w->race,
            $w->trained,
            $w->primary_role,
            $w->highest_edu


        ];
    }

    public function headings(): array
    {
        return [
            "SNo",
            "Name",
            "Email",
            "phone",
            "status",
            "created at",
            "District",
            "Building",
            "address",
            "date of Application",
            "gender",
            "Are you Hispanic",
            "Race",
            "Trained",
            "Primary Role",
            "level of education"
        ];
    }
}
