<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Department;
use App\Models\Student;
use App\Models\StudentsData;
use DateTime;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        //
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

    public function updateData(Request $request) 
    {
        $birth_dates = DateTime::createFromFormat('m/d/Y', $request->birth_date)->format('Y/m/d');
        $student = Student::find($request->id);
        $student->address = $request->address;
        $student->birth_date = $birth_dates;
        $student->updated_at = now();
        $student->save();
        session(['birth_date' => $birth_dates]);
        session(['address' => $request->address]);
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
