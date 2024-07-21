<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentExport implements
    FromCollection,
    WithHeadings,
    WithMapping
{

    protected $cohortId;

    public function __construct($cohortId)
    {
        $this->cohortId = $cohortId;
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $student = Student::select(
               "*", 
               "tutor_user.name as tutor_name", 
               "tutor_user.email as tutor_email",
               "teacher_user.name as teacher_name" ,
               "students.created_at as create_date", 
               "students.updated_at as updated_date"
            )
            ->join("tutors", "tutors.tutor_id", "=", "students.tutor_id", "left")
            ->join("users as tutor_user", "tutor_user.id", "=", "tutors.user_id", "left")
            ->join("teachers", "teachers.teacher_id", "=", "tutors.tutor_id", "left")
            ->join("users as teacher_user", "teacher_user.id", "=", "teachers.user_id", "left")

            ->join("schools", "schools.school_id", "=", "teachers.school_id", "left")
            ->join("sitecoordinator", "sitecoordinator.university_id", "=", "schools.university_id", "left")
            ->join('cohorts', 'students.cohort_id', 'LIKE', DB::raw("CONCAT('%',cohorts.cohort_id,'%')"), "left")

            ->whereRaw("FIND_IN_SET(?, cohorts.cohort_id)", [$this->cohortId])
            ->get();
        // dd($student);
        return $student;
    }

    public function headings(): array
    {
        return [
            "Student Id",
            "Student Name",
            "Student’s DCPS",
            "Study Group",
            "Site Coordinator",
            "Teacher Name",
            "School Name",
            "Tutor Id",
            "Tutor Name",
            "Tutor Email",
            "Start Date",
            "Wave",
            "What is this child`s sex",
            "What is this child’s dob",
            "What grade is this child currently in",
            "Is this child Hispanic or Latino/Latina",
            "What is this child`s race",
            "Some other race",
            "Does this child speak a language other than English at home",
            "What other language does this child speak",
            "Other language",
            "What is this child’s cost for school meals",
            "Does this child have a documented disability",
            " What is this child`s documented disability",
            "Other disability",
            "Does this child have an IEP for reading",
            "Was this student a participant in the project last year and continuing as a participant this year",
            "What was the student’s ID number last year (to be completed by EIR office staff)",
            "Comments",
            "Created Date",
            "Updated Date",
        ];
    }

    public function map($w): array
    {
        // dd($w);
        log::info($w);
        return [
            $w->student_id,
            $w->student_name,
            $w->dcps_id,
            "Read Usa",
            $w->university_name,
            $w->teacher_name,
            $w->school_name,
            $w->tutor_id,
            $w->tutor_name,
            $w->tutor_email,
            $w->word_test,
            $w->start_test_date,
            $w->book_level,
            $w->hand2mind,
            $w->hand2mind_date,
            $w->which_hand2_mind_assessment,
            $w->hand2mind_lesson,
            $w->garfieldassessment,
            $w->garfield_date,
            $w->recreational_reading_raw_score,
            $w->academice_reading_raw_score,
            $w->full_scale_raw_score,
            $w->gortassessment,
            $w->gort_date,
            $w->rate_row_score,
            $w->accuracy_raw_score,
            $w->fluency_raw_score,
            $w->comprehension_raw_score,
            $w->ctoppassessment,
            $w->ctopp_date,
            $w->ctopp_elison_raw_score,
            $w->ctopp_blending_words_raw_score,
            $w->ctopp_sound_matching_raw_score,
            $w->ctopp_phoneme_isolation_raw_score,
            $w->ctopp_memory_for_digit_raw_score,
            $w->ctopp_nonword_repetition_raw_score,
            $w->ctopp_rapid_digit_naming_raw_score,
            $w->ctopp_rapid_letter_naming_raw_score,
            $w->ctopp_rapid_color_naming_raw_score,
            $w->ctopp_rapid_object_naming_raw_score,
            $w->ctopp_blending_nonwords_raw_score,
            $w->ctopp_segmenting_nonwords_raw_score,
            $w->observation,
            $w->observation_survey_date,
            $w->observation_entry_letter,
            $w->observation_entry_word,
            $w->observation_entry_concept,
            $w->observation_entry_writing,
            $w->observation_entry_hearing,
            $w->observation_entry_text_reading,
            $w->create_date,
            $w->updated_date,
        ];
    }

   
}
