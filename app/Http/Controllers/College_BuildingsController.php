<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\College;
use App\Models\Colleges_Buildings;
use App\Models\Colleges_buildings as ModelsColleges_buildings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class College_BuildingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $colleges_buildings = Colleges_Buildings::orderBy('college_id', 'asc')->get();
        $data = DB::select('SELECT college_id, COUNT(college_id) AS college_count FROM colleges_buildings GROUP BY college_id');
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }

        $colleges = College::all();
        $buildings = Building::all();
        
        return view('admins.setup.colleges_buildings', ['buildings' => $buildings, 'colleges' => $colleges, 'data' => $data, 'colleges_buildings' => $colleges_buildings]);
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
        $colleges =  Colleges_Buildings::all()->where('college_id', $request->college_id);
        if ($colleges->count() > 0) {
            foreach ($colleges as $college) {
                $college->delete();
            }
        }
        $validator = Validator::make($request->all(), [
            'college_id' => 'required|integer',
            'buildings' => 'required|json',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }
        $data = json_decode($request->buildings);
        
        foreach ($data as $building) {
            $college_building = new Colleges_Buildings();
            $college_building->college_id = $request->college_id;
            $college_building->building_id = $building;
            $college_building->save();
        }

        return redirect('/colleges_buildings');
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
