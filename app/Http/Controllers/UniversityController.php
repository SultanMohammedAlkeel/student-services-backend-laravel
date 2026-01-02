<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\College;
use App\Models\Course;
use App\Models\Colleges_buildings;
use App\Models\Department;
use App\Models\Hall;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\University;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        return view('admins.management.university', [
            'university' => University::first(), 
            'colleges' => College::all(),
            'departments' => Department::all(),
            'teachers' => Teacher::all(),
            'students' => Student::all(),
            'colleges_buildings' => Colleges_buildings::all(),
            'buildings' => Building::all(),
            'halls' => Hall::all(),
        ]);
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
        $university = new University();
        $imageName = '';

        $image = $request->file('logo_url');
        $imgName = rand(100000, 999999) . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('', $imgName, 'logo');
        $imageName = 'images/logo/'. $path;

        $university->name = $request->name;
        $university->email = $request->email;
        $university->contact_info = $request->contact_info;
        $university->description = $request->description;
        $university->website = $request->website;
        $university->logo_url = $imageName;
        $university->save();
        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        
        $college = College::find($id);
        $levels = ['المستوى الاول','المستوى الثاني','المستوى الثالث','المستوى الرابع','المستوى الخامس','المستوى السادس','المستوى السابع'];

        return view('admins.management.college', [
            'college' => $college,
            'departments' => Department::where('college_id', $college->id)->get(),
            'teachers' => Teacher::where('college_id', $college->id)->get(),
            'students' => Student::all(),
            'courses' => Course::all(),
            'levels' => $levels,
            
        ]);
    }
    public function showStudent(Request $request)
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        $students = Student::where('level', $request->level)->where('department_id', $request->department_id)->get();
        $department = Department::find($request->department_id);

        return view('admins.management.students', [
            'departments' => Department::all(),
            'students' => $students,
            'college' => College::find($department->college_id)->name,
            'department' => $department->name,
            'level' => $request->level,

        ]);
    }
    public function showCourse(Request $request)
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        $courses = Course::where('level', $request->level)->where('department_id', $request->department_id)->get();
        $department = Department::find($request->department_id);

        return view('admins.management.courses', [
            'courses' => $courses,
            'college' => College::find($department->college_id)->name,
            'department' => $department->name,
            'level' => $request->level,

        ]);
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
