<?php

namespace App\Exports;

use App\Models\Tutor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TutorExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // return Tutor::all();
        return Tutor::select('*')
            ->join("users", "users.id", "=", "tutors.user_id", "left")
            ->join("details", "details.details_id", "=", "tutors.details_id", "left")->get();
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
            "Sno",
            "Name",
            "Email",
            "Phone",
            "Status",
            "created at",
            "District",
            "Building",
            "Address",
            "date of Application",
            "gender",
            "Are you Hispanic",
            "Race",
            "Trained",
            "Primary Role",
            "level of education",

        ];
    }
}
