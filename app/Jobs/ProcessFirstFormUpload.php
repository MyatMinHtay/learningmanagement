<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\FirstForm;
use App\Models\StudentInformation;
use App\Models\Courses_Student;
use Illuminate\Support\Facades\Storage;

class ProcessFirstFormUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    public $formData;
    public $courseId;
    public $filePaths;
    public $studentsData;
    public $selectedStudents;

    public function __construct($formData, $courseId, $filePaths, $studentsData, $selectedStudents)
    {
        $this->formData = $formData;
        $this->courseId = $courseId;
        $this->filePaths = $filePaths;
        $this->studentsData = $studentsData;
        $this->selectedStudents = $selectedStudents;
    }

    


    public function handle()
    {
        // Add attached file paths
        $this->formData['attachedFilePaths'] = json_encode($this->filePaths);

        // Save FirstForm
        $firstForm = FirstForm::create($this->formData);

        // Save students
        foreach ($this->studentsData as $student) {
            $stu = StudentInformation::create($student);
            $this->selectedStudents[] = $stu->id;
        }

        // Attach students to course
        foreach ($this->selectedStudents as $studentId) {
            Courses_Student::create([
                'courses_id' => $this->courseId,
                'student_id' => $studentId,
            ]);
        }
    }

}
