<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CollegeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        
        $universities = University::all();
        $colleges = College::all();
        return view('admins.setup.college', ['universities' => $universities, 'colleges' => $colleges]);
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
        $college = new College();
        $imageName = '';

        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'university_id' => 'required|integer',
            'contact_info' => 'required|string|max:255',
            'logo_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $image = $request->file('logo_url');
        $imgName = rand(100000, 999999) . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('', $imgName, 'logo');
        $imageName = 'images/logo/'. $path;

        $college->name = $request->name;
        $college->university_id = $request->university_id;
        $college->contact_info = $request->contact_info;
        $college->logo_url = $imageName;
        $college->save();
        return redirect('/college');
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        // $colleges = College::where('name', 'like', '%' . $keyword . '%')->get();

        $colleges = DB::table('colleges')
        ->join('universities', 'colleges.university_id', '=', 'universities.id')
        ->select('colleges.id', 'colleges.name as college_name', 'universities.name as university_name', 'colleges.contact_info')
        ->where('colleges.name', 'like', '%' . $keyword . '%')
        ->get();
        return response()->json($colleges);
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
