<?php


namespace App\Http\Controllers;

use App\Models\Academic_year;
use App\Models\AcademicYear;
use DateTime;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        $academic_years = AcademicYear::all();
        
        return view('admins.setup.academic_year', ['academic_years' => $academic_years ]);
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

        $start_date = DateTime::createFromFormat('d/m/Y', $request->start_date)->format('Y'); // تحويل السلسلة إلى كائن DateTime
        $end_date = DateTime::createFromFormat('d/m/Y', $request->end_date)->format('Y'); // تحويل السلسلة إلى كائن DateTime
        
        $academic_years = new AcademicYear();
        $academic_years->start_date = $start_date;
        $academic_years->end_date = $end_date;
        $academic_years->status = true;
        $academic_years->save();

        return redirect('/academic-year');

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
