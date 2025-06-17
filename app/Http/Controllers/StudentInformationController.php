<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\StudentInformation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\File;

class StudentInformationController extends Controller
{

    public function search(Request $request)
    {
        $students = StudentInformation::where(function ($query) use ($request) {
                        $query->where('studentName', 'LIKE', '%' . $request->name . '%')
                            ->orWhere('sirb', 'LIKE', '%' . $request->name . '%');
                    })
                    ->select('id', 'studentName', 'sirb', 'studentDOB')
                    ->get();

        return response()->json($students);
    }
    public function index(){

        $user = auth()->user();

        $students = StudentInformation::paginate(20)->withQueryString();


        if ($user->role->role === 'adminstrator' || $user->role->role === 'moderator') {
            return view('admin.student.adminstudentview',[
                'students' => $students
            ]);
        }else{
            return view('admin.student.index',[
                'students' => $students,
            ]);
        }


        
    }

    public function createstudent(){

        return view('admin.student.create');
    }

    public function storestudent(Request $request){

        $formData = $request->validate([
            'studentName' => 'required',
            'sirb' => 'required',
            'studentDOB' => 'nullable',
            'allInOne' => 'required|file|mimes:pdf|max:20480',
        ]);

  

       
        $studentFileFields = ['allInOne'];
        

        foreach ($studentFileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
        
                // Get original name and sanitize it
                $originalName = $file->getClientOriginalName();
                $cleanName = preg_replace('/\s+/', '_', $originalName); // Replace spaces with underscores
                $cleanName = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $cleanName); // Remove unwanted characters
        
                // Prepend timestamp for uniqueness
                $fileName = time() . '_' . $cleanName;
        
                $studentfoldername = $formData['sirb'];

                $uploadPath = public_path("assets/students/{$studentfoldername}");
        
                // Create directory if it doesn't exist
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
        
                // Move file to desired location
                $file->move($uploadPath, $fileName);
        
                // Save relative path
                $formData[$field] = "assets/students/{$studentfoldername}/$fileName";
            }
        }

        try {
            $student = StudentInformation::create($formData);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                // Duplicate entry
                return back()->withErrors(['error' => 'The student SIRB already exists.'])->withInput();
            }

            return back()->withErrors(['error' => 'Failed to create student: ' . $e->getMessage()]);
        }
    
        

        return redirect()->route('students')->with('success', 'Student Created Successfully');
    }   

    public function editstudent($id){

        $student = StudentInformation::find($id);


        return view('admin.student.edit',[
            'student' => $student
        ]);
    }

    public function updatestudent(Request $request, $id)
    {
        $student = StudentInformation::findOrFail($id);

        $formData = $request->validate([
            'studentDOB' => 'nullable',
            'studentName' => 'required',
            'sirb'          => [
                'required',
                Rule::unique('student_information')->ignore($student->id),
            ],
            'allInOne' => 'nullable|file|mimes:pdf|max:20480',
        ]);

        


        $studentFileFields = ['allInOne'];

        foreach ($studentFileFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if exists

                $filePath = public_path($student->$field); // assuming these fields store relative paths like "assets/students/field/filename.pdf"
            
                if (!empty($student->$field) && file_exists($filePath)) {
                   
                    unlink($filePath);
                }

                $file = $request->file($field);
                $originalName = $file->getClientOriginalName();
                $cleanName = preg_replace('/\s+/', '_', $originalName); // Replace spaces with underscores
                $cleanName = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $cleanName); // Remove unwanted characters
        
                // Prepend timestamp for uniqueness
                $fileName = time() . '_' . $cleanName;

                $studentFolderName = $formData['sirb'];

                $uploadPath = public_path("assets/students/{$studentFolderName}");

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                $file->move($uploadPath, $fileName);

                $student->$field = "assets/students/{$studentFolderName}/{$fileName}";
            }
        }


        
        $student->studentName = $formData['studentName'];
        $student->sirb = $formData['sirb'];
        $student->studentDOB = $formData['studentDOB'];
        try {
            $student->update();
        } catch (QueryException $e) {
            return back()->withErrors(['error' => 'Failed to update student: ' . $e->getMessage()]);
        }

        return redirect()->route('students')->with('success', 'Student updated successfully.');
    }


    public function destorystudent($id)
    {
        $student = StudentInformation::find($id);

        if (!$student) {
            return redirect()->route('students')->with('error', 'Student not found.');
        }

        

        try {
            $studentFileFields = ['allInOne'];

            foreach ($studentFileFields as $field) {
                $filePath = public_path($student->$field); // assuming these fields store relative paths like "assets/students/field/filename.pdf"
                
                if (!empty($student->$field) && file_exists($filePath)) {
                    unlink($filePath);
                }
                
            }

            $studentFolderName = $student->sirb;
                $folderPath = public_path("assets/students/{$studentFolderName}");

                if (File::exists($folderPath)) {
                    File::deleteDirectory($folderPath);
                }

            $student->delete();

        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) {
                return back()->withErrors(['error' => 'This student is enrolled in a course and cannot be deleted.'])->withInput();
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('students')->with('success', 'Student Deleted Successfully');
    }

}
