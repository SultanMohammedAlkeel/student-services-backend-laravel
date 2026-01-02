<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Department;
use App\Models\Exam;
use App\Models\ExamRecord;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Debug\VirtualRequestStack;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    var $examType = [
        'صح و خطأ' => '<i class="fas fa-check-double" style="color: #00b894; font-size: 80px"></i>',
        'اختيارات' => '<i class="fas fa-list-check" style="color: #0984e3; font-size: 80px"></i>'
    ];
    public function index()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        if (session('user_type') == 'معلم') {
            $exams = Exam::where('created_by', session('user_id'))->get();
        } else {
            $examRecords = ExamRecord::all()->where('student_id', session('user_id'))->pluck('exam_id');
            $exams = Exam::whereNotIn('id', $examRecords)->where('department_id', session('department_id'))->where('level', session('level'))->get();
        }
        
        $departments = Department::all();
        $colleges = College::all();
        $records = ExamRecord::all();
        $levels = ['المستوى الاول','المستوى الثاني','المستوى الثالث','المستوى الرابع','المستوى الخامس','المستوى السادس','المستوى السابع'];
        $types = ['اختيارات', 'صح و خطأ'];
        $languages = ['عربي', 'انجليزي'];
        return view('exam.all-exam', [
            'exams' => $exams, 
            'departments' => $departments, 
            'colleges' => $colleges, 
            'levels' => $levels,
            'types' => $types,
            'languages' => $languages,
            'users' => User::all(),
            'records' => $records,
            'examType' => $this->examType,
        ]);
    }
    
    public function MyExam()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        $examRecords = ExamRecord::all()->where('student_id', session('user_id'))->pluck('exam_id');

        $exams = Exam::whereIn('id', $examRecords)->get();
        $departments = Department::all();
        $colleges = College::all();
        $levels = ['المستوى الاول','المستوى الثاني','المستوى الثالث','المستوى الرابع','المستوى الخامس','المستوى السادس','المستوى السابع'];
        $types = ['اختيارات', 'صح و خطأ'];
        $languages = ['عربي', 'انجليزي'];
        return view('exam.my-exam', [
            'exams' => $exams, 
            'departments' => $departments, 
            'colleges' => $colleges, 
            'levels' => $levels,
            'types' => $types,
            'languages' => $languages,
            'users' => User::all(),
            'record' => ExamRecord::where('student_id', session('user_id'))->get(),
            'records' => ExamRecord::all(),
            'examType' => $this->examType,
        ]);
    }

    function AddExam()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }

        $exams = Exam::all();
        $departments = Department::all();
        $colleges = College::all();
        $levels = ['المستوى الاول','المستوى الثاني','المستوى الثالث','المستوى الرابع','المستوى الخامس','المستوى السادس','المستوى السابع'];
        $types = ['اختيارات', 'صح و خطأ'];
        $languages = ['عربي', 'انجليزي'];
        return view('exam.add-exam', [
            'exams' => $exams, 
            'departments' => $departments, 
            'colleges' => $colleges, 
            'levels' => $levels,
            'types' => $types,
            'languages' => $languages,
            'users' => User::all(),
        ]);
    }

    function ExamResult(Request $request)
    {
        $takedexamRecord = ExamRecord::where('exam_id', $request->exam_id)->where('student_id', session('user_id'))->first();
        if ($takedexamRecord) {
            $exam = Exam::find($request->exam_id);
            return view('exam.result', [
                'exam_name' => $exam->name,
                'language' => $exam->language,
                'correct' => $request->correct,
                'wrong' => $request->wrong,
                'score' => $request->score,
                'exam_code' => $exam->code,
            ]);
        }
        $examRecord = new ExamRecord();
        $examRecord->exam_id = $request->exam_id;
        $examRecord->student_id = session('user_id');
        $examRecord->score = $request->score;
        $examRecord->correct = $request->correct;
        $examRecord->wrong = $request->wrong;
        $examRecord->answers = ($request->answers);
        $examRecord->save();
        $exam = Exam::find($request->exam_id);
        return view('exam.result', [
            'exam_name' => $exam->name,
            'language' => $exam->language,
            'correct' => $request->correct,
            'wrong' => $request->wrong,
            'score' => $request->score,
            'exam_code' => $exam->code,
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
        $exam = new Exam();
        $exam->code = rand(1000000, 9999999);
        $exam->name = $request->name;
        $exam->description = $request->description;
        $exam->language = $request->language;
        $exam->type = $request->type;
        $exam->department_id = $request->department_id;
        $exam->level = $request->level;
        $exam->created_by = session('user_id');
        $exam->exam_data = $request->exam_data;
        $exam->save();
        return redirect('/exam');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $exam = Exam::where('code', $id)->first();
        $qusetions = json_decode($exam->exam_data, true);
        
        return view('exam.show-exam', [
            'exam' => $exam,
            'departments' => Department::all(),
            'colleges' => College::all(),
            'qusetions' => $qusetions,
        ]);
    }

    function ShowMyExam(string $id)  
    {
        $exam = Exam::where('code', $id)->first();
        $qusetions = json_decode($exam->exam_data, true);
        $record = ExamRecord::where('exam_id', $exam->id)->where('student_id', session('user_id'))->first();
        return view('exam.show-my-exam', [
            'exam' => $exam,
            'departments' => Department::all(),
            'colleges' => College::all(),
            'qusetions' => $qusetions,
            'record' => $record,
            'answers' => json_decode($record->answers, true),
            'score' => $record->score,
        ]);
    }

    function ListStudents(string $id)
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        
        $exam = Exam::where('code', $id)->first();
        $examRecord = ExamRecord::where('exam_id', $exam->id)->orderBy('score', 'desc')->get();
        return view('exam.list-students', [
            'exam' => $exam,
            'departments' => Department::all(),
            'examRecord' => $examRecord,
            'users' => User::all(),
            'students' => Student::all(),
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
