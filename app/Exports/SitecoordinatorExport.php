<?php

namespace App\Exports;

use App\Models\sitecoordinator;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SitecoordinatorExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return SiteCoordinator::select('*')
            ->join("users", "users.id", "=", "sitecoordinator.user_id", "left")
            ->join("details", "details.details_id", "=", "sitecoordinator.details_id", "left")->get();
    }


    public function map($w): array
    {

        static $count = 0;
        $count++;
        return [

            $count,
            $w->university_name,
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
            "S.No",
            "Sitecoordinator Name",
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
