<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }

        $courses = Course::orderBy('id', 'desc')->get();
        $departments = Department::all();
        $colleges = College::all();
        $levels = ['المستوى الاول','المستوى الثاني','المستوى الثالث','المستوى الرابع','المستوى الخامس','المستوى السادس','المستوى السابع'];
        $types = ['متطلب','مقرر','عام'];    
        $terms = ['الاول','الثاني'];
        return view('admins.setup.course', ['courses' => $courses, 'departments'=> $departments, 'colleges' => $colleges, 'levels' => $levels, 'types' => $types, 'terms' => $terms]);
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
        
        $course = new Course();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'department_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $course->name = $request->name;
        $course->type = $request->type;
        $course->department_id  = $request->department_id;
        $course->level = $request->level;
        $course->term = $request->term;
        $course->description = $request->description;
        $course->save();

        return redirect('/course');
    }
    
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $courses = DB::table('courses')
        ->join('departments', 'courses.department_id', '=', 'departments.id')
        ->select('courses.id', 'courses.name as course_name', 'departments.name as department_name', 'level', 'type')
        ->where('courses.name', 'like', '%' . $keyword . '%')
        ->get();
        return response()->json($courses);
    }

    public function getDepartments($college_id)
    {
        // جلب الأقسام التابعة للكلية المحددة
        $departments = Department::where('college_id', $college_id)->get();
        return response()->json($departments);
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
