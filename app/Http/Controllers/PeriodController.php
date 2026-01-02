<?php

namespace App\Http\Controllers;

use App\Models\Academic_year;
use App\Models\AcademicYear;
use App\Models\Period;
use DateTime;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        $academic_years = AcademicYear::where('status', 1)->get();
        $periods = Period::where('academic_year_id', $academic_years->first()->id)->get();
        return view('admins.management.academic_period', [
            'academic_years' => $academic_years,
            'periods' => $periods
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
        $start_date_1 = DateTime::createFromFormat('m/d/Y', $request->start_date_1)->format('Y/m/d');
        $start_date_2 = DateTime::createFromFormat('m/d/Y', $request->start_date_2)->format('Y/m/d');
        $end_date_1 = DateTime::createFromFormat('m/d/Y', $request->end_date_1)->format('Y/m/d');
        $end_date_2 = DateTime::createFromFormat('m/d/Y', $request->end_date_2)->format('Y/m/d');

        $period1 = new Period();
        $period1->academic_year_id = $request->academic_year;
        $period1->term = $request->term_1;
        $period1->start_date = $start_date_1;
        $period1->end_date = $end_date_1;
        $period1->save();
        
        $period2 = new Period();
        $period2->academic_year_id = $request->academic_year;
        $period2->term = $request->term_2;
        $period2->start_date = $start_date_2;
        $period2->end_date = $end_date_2;
        $period2->save();
        
        return redirect('periods');
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
        // 'period' => $period
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }
    public function updatePeriod(Request $request, string $id)
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        
        
        $start_date_1 = DateTime::createFromFormat('m/d/Y', $request->start_date_1)->format('Y/m/d');
        $start_date_2 = DateTime::createFromFormat('m/d/Y', $request->start_date_2)->format('Y/m/d');
        $end_date_1 = DateTime::createFromFormat('m/d/Y', $request->end_date_1)->format('Y/m/d');
        $end_date_2 = DateTime::createFromFormat('m/d/Y', $request->end_date_2)->format('Y/m/d');

        
        $period = Period::where('academic_year_id', $id)->get();
        $period1 = Period::find($period[0]->id);
        $period1->academic_year_id = $request->academic_year;
        $period1->term = $request->term_1;
        $period1->start_date = $start_date_1;
        $period1->end_date = $end_date_1;
        $period1->save();
        
        $period2 = Period::find($period[1]->id);
        $period2->academic_year_id = $request->academic_year;
        $period2->term = $request->term_2;
        $period2->start_date = $start_date_2;
        $period2->end_date = $end_date_2;
        $period2->save();
        
        return redirect('periods');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $period = Period::find($id);
        $period->delete();
        return redirect('periods');
    }
}
