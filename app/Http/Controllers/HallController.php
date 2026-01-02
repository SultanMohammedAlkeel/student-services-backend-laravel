<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\College;
use App\Models\Colleges_buildings;
use App\Models\Department;
use App\Models\Hall;
use App\Models\HallBooking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HallController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        
        $halls = hall::all();
        $buildings = Building::all();
        return view('admins.setup.hall', ['halls' => $halls, 'buildings' => $buildings]);
    }

    public function HallBooking()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        
        $college = College::find(session('college'));
        $colleges_buildings = Colleges_buildings::where('college_id', session('college'))->pluck('building_id');
        $buildings = Building::whereIn('id', $colleges_buildings)->get();
        $halls = Hall::all();

        // احصل على جميع الحجوزات التي تم إنشاؤها اليوم أو غدًا
        // باستخدام Carbon
        // احصل على بداية اليوم الحالي ونهاية يوم غد
        $startOfDay = Carbon::now()->startOfDay(); 
        $endOfTomorrow = Carbon::now()->addDay()->endOfDay(); 
        $hall_bookings = HallBooking::whereBetween('created_at', [$startOfDay, $endOfTomorrow])->get();
        
        $periods = ['08:00 - 10:00','10:00 - 12:00','12:00 - 02:00'];

        return view('students.pages.hall-booking', [
            'halls' => $halls, 
            'buildings' => $buildings,
            'hall_bookings' => $hall_bookings,
            'college' => $college,
            'periods' => $periods,
            'departments' => Department::all()
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
        $hall = new Hall();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'building_id' => 'required|integer',
            'type' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // إذا وصلت إلى هنا، فهذا يعني أن البيانات صالحة
        $validatedData = $validator->validated();

        $hall->name = $request->name;
        $hall->capacity = $request->capacity;
        $hall->building_id = $request->building_id;
        $hall->type = $request->type;
        $hall->save();

        return redirect('/hall');
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $halls = DB::table('halls')
        ->join('buildings', 'halls.building_id', '=', 'buildings.id')
        ->select('halls.id', 'halls.name as hall_name', 'buildings.name as building_name', 'halls.type', 'halls.capacity')
        ->where('halls.name', 'like', '%' . $keyword . '%')
        ->get();
        return response()->json($halls);
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
