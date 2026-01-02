<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Department;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        $colleges = College::all();
        $departments = Department::all();
        $teachers = Teacher::all();
        
        return view('admins.setup.teacher', ['departments' => $departments, 'colleges' => $colleges, 'teachers' => $teachers]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $teachers = new Teacher();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'gender' => 'required|string|max:255',
            'college_id' => 'required|integer',
            'department_id' => 'required|integer',
            'academic_degree' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $teachers->name = $request->name;
        $teachers->code = rand(1000000, 9999999);
        $teachers->gender = $request->gender;
        $teachers->college_id = $request->college_id;
        $teachers->department_id = $request->department_id;
        $teachers->academic_degree = $request->academic_degree;
        $teachers->specialization = $request->specialization;
        $teachers->save();

        return redirect('/teacher');
    }

    public function getDepartments($college_id)
    {
        // جلب الأقسام التابعة للكلية المحددة
        $departments = Department::where('college_id', $college_id)->get();
        return response()->json($departments);
    }
    
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        
        $teachers = DB::table('teachers')
        ->join('colleges', 'teachers.college_id', '=', 'colleges.id')
        ->join('departments', 'teachers.department_id', '=', 'departments.id')
        ->select('teachers.id', 'teachers.name as teacher_name', 'colleges.name as college_name', 'departments.name as department_name', 'teachers.academic_degree')
        ->where('teachers.name', 'like', '%' . $keyword . '%')
        ->get();
        return response()->json($teachers);
    }

    public function MyStudents(Request $request) {
        $students = Student::where('level', $request->level)->where('department_id', $request->department_id)->get();

        return view('teacher.pages.my-students', [
            'students' => $students,
            'departments' => Department::all()
        ]);
    }

    public function putGrade(Request $request) {
        $students = Student::where('level', $request->level)->where('department_id', $request->department_id)->get();

        return view('teacher.pages.put-grade', [
            'students' => $students,
            'departments' => Department::all()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
