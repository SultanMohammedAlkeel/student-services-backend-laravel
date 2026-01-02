<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
    
        $buildings = Building::all();
        
        
        return view('admins.setup.building', ['buildings' => $buildings]);
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
        
        $building = new Building();

        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $building->name = $request->name;
        $building->location = $request->location;
        $building->description = $request->description;
        $building->save();

        return redirect('/building');
    }
    
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        
        $buildings = Building::where('name', 'like', '%' . $keyword . '%')->get();
        return response()->json($buildings);
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
