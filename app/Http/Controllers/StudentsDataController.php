<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Department;
use App\Models\Student;
use App\Models\StudentsData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StudentsDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $studentsData = StudentsData::all();
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        
        return view('admins.setup.students_data', ['studentsData' => $studentsData]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function uploadStudents($id) {
        $studentsData = StudentsData::find($id);
        
        if (!$studentsData) {
            return;
        }

        $students = json_decode($studentsData->data, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return;
        }
        
        $levels = ['المستوى الاول','المستوى الثاني','المستوى الثالث','المستوى الرابع','المستوى الخامس','المستوى السادس','المستوى السابع'];
        
        foreach ($students as $student) {
            $check = Student::where('card', $student['card'])->first();
            if ($check) {
                continue;
            }
            $levelIndex = ($student['level'] - 1);
            $card = $student['card'];
            $gender = $student['gender'];
            $name = $student['name'];
            $qualification = $student['qualification'];
            $enrollment_year = $student['enrollment_year'];
            $department = $student['department'];
            $active_code = $student['active_code'];

            $student = new Student();
            $student->card = $card;
            $student->name = $name;
            $student->gender = $gender;
            $student->level = $levels[$levelIndex];
            $student->qualification = $qualification;
            $student->enrollment_year = $enrollment_year;
            $student->department_id = $department; 
            $student->active_code = $active_code;
            $student->save();
        }

        return redirect('/');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $studentsData = new StudentsData();
        $validator = Validator::make($request->all(), [
            'data' => 'required|file|mimes:json', // الملف مطلوب ويجب أن يكون من نوع JSON
        ]);
        // قراءة الملف وتحويله إلى مصفوفة
        $file = $request->file('data');
        $fileContent = file_get_contents($file->getRealPath());
        $jsonData = json_decode($fileContent, true); // تحويل JSON إلى مصفوفة
    
        $studentsData->name = $request->name;
        $studentsData->data = json_encode($jsonData['data']);
        $studentsData->status = true;
        $studentsData->save();
        

        return redirect('/');


    }
    public function getData(Request $request)
    {
        
        $students = StudentsData::all()->where('status', true)->first()->data;
        // بيانات مثاليه
        $data = json_decode($students, true);

        // البحث عن حقول معينة عبر الاسم
        $search = $request->input('department_id');
        if ($search) {
            $data = array_filter($data, function ($item) use ($search) {
                return stripos($item['department'], $search) !== false;
            });
        }

        return response()->json(array_values($data));
    }

    public function getDepartments($college_id)
    {
        $departments = Department::where('college_id', $college_id)->get();
        return response()->json($departments);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $colleges = College::all();
        $departments = Department::all();
        $data = StudentsData::all()->where('status', true)->where('id', $id)->first()->data;
        $level = ['الاول', 'الثاني', 'الثالث', 'الرابع', 'الخامس'];
        return view('admins.setup.students', ['colleges' => $colleges, 'departments' => $departments, 'data' => json_decode($data, true), 'level' => $level, 'id' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function updateData(Request $request) {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
