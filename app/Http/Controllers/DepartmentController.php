<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
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
        return view('admins.setup.department', ['departments' => $departments, 'colleges' => $colleges]);
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
        $departments = new Department();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:255',
            'college_id' => 'required|integer',
            'levels' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $departments->name = $request->name;
        $departments->short_name = $request->short_name;
        $departments->college_id = $request->college_id;
        $departments->levels = $request->levels;
        $departments->description = $request->description;
        $departments->save();

        return redirect('/department');
    }
    
    /**
     * Display the specified resource.
     */
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        
        $departments = DB::table('departments')
        ->join('colleges', 'departments.college_id', '=', 'colleges.id')
        ->select('departments.id', 'departments.name as department_name', 'colleges.name as college_name', 'departments.short_name')
        ->where('departments.name', 'like', '%' . $keyword . '%')
        ->get();
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
