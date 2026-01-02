<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;

class ExamController extends Controller
{
    /**
     * الحصول على قائمة الامتحانات مع تطبيق الفلتر
     */
    public function index(Request $request)
    {
        $query = DB::table('exams')
            ->leftJoin('departments', 'exams.department_id', '=', 'departments.id')
            ->select(
                'exams.*',
                'departments.name as department_name'
            );
        
        // تطبيق الفلتر على الاستعلام
        if ($request->has('search_query') && !empty($request->search_query)) {
            $query->where(function($q) use ($request) {
                $q->where('exams.name', 'like', '%' . $request->search_query . '%')
                  ->orWhere('exams.description', 'like', '%' . $request->search_query . '%');
            });
        }
        
        if ($request->has('type') && !empty($request->type)) {
            $query->where('exams.type', $request->type);
        }
        
        if ($request->has('level') && !empty($request->level)) {
            $query->where('exams.level', $request->level);
        }
        
        if ($request->has('language') && !empty($request->language)) {
            $query->where('exams.language', $request->language);
        }
        
        if ($request->has('department_id') && !empty($request->department_id)) {
            $query->where('exams.department_id', $request->department_id);
        }
        
        // تطبيق الترتيب
        $sortBy = $request->has('sort_by') ? $request->sort_by : 'exams.created_at';
        $ascending = $request->has('ascending') ? filter_var($request->ascending, FILTER_VALIDATE_BOOLEAN) : false;
        
        $query->orderBy($sortBy, $ascending ? 'asc' : 'desc');
        
        // الحصول على النتائج
        $exams = $query->get();
        
        // إضافة معلومات إضافية لكل امتحان
        $exams->each(function($exam) {
            $exam->questions_count = $this->getQuestionsCount($exam);
            $exam->taken_count = DB::table('exam_records')
                ->where('exam_id', $exam->id)
                ->count();
        });
        
        return response()->json([
            'status' => true,
            'exams' => $exams
        ]);
    }
    
    /**
     * الحصول على الامتحانات الشائعة
     */
    public function getPopularExams()
    {
        // الحصول على الامتحانات الأكثر شعبية (الأكثر إجراءً)
        $popularExamIds = DB::table('exam_records')
            ->select('exam_id', DB::raw('COUNT(*) as taken_count'))
            ->groupBy('exam_id')
            ->orderBy('taken_count', 'desc')
            ->limit(10)
            ->pluck('exam_id');
        
        $exams = DB::table('exams')
            ->leftJoin('departments', 'exams.department_id', '=', 'departments.id')
            ->select(
                'exams.*',
                'departments.name as department_name'
            )
            ->whereIn('exams.id', $popularExamIds)
            ->get();
        
        // إضافة معلومات إضافية لكل امتحان
        $exams->each(function($exam) {
            $exam->questions_count = $this->getQuestionsCount($exam);
            $exam->taken_count = DB::table('exam_records')
                ->where('exam_id', $exam->id)
                ->count();
        });
        
        return response()->json([
            'status' => true,
            'exams' => $exams
        ]);
    }
    
    /**
     * الحصول على أحدث الامتحانات
     */
    public function getRecentExams()
    {
        $exams = DB::table('exams')
            ->leftJoin('departments', 'exams.department_id', '=', 'departments.id')
            ->select(
                'exams.*',
                'departments.name as department_name'
            )
            ->orderBy('exams.created_at', 'desc')
            ->limit(10)
            ->get();
        
        // إضافة معلومات إضافية لكل امتحان
        $exams->each(function($exam) {
            $exam->questions_count = $this->getQuestionsCount($exam);
            $exam->taken_count = DB::table('exam_records')
                ->where('exam_id', $exam->id)
                ->count();
        });
        
        return response()->json([
            'status' => true,
            'exams' => $exams
        ]);
    }
    
    /**
     * الحصول على امتحانات المستخدم
     */
    public function getMyExams()
    {
        $userId = Auth::id();
        
        // الحصول على الامتحانات التي قام المستخدم بإجرائها
        $examIds = DB::table('exam_records')
            ->where('student_id', $userId)
            ->orderBy('created_at', 'desc')
            ->pluck('exam_id');
        
        $exams = DB::table('exams')
            ->leftJoin('departments', 'exams.department_id', '=', 'departments.id')
            ->select(
                'exams.*',
                'departments.name as department_name'
            )
            ->whereIn('exams.id', $examIds)
            ->get();
        
        // إضافة معلومات إضافية لكل امتحان
        $exams->each(function($exam) {
            $exam->questions_count = $this->getQuestionsCount($exam);
            $exam->taken_count = DB::table('exam_records')
                ->where('exam_id', $exam->id)
                ->count();
        });
        
        return response()->json([
            'status' => true,
            'exams' => $exams
        ]);
    }
    
    /**
     * الحصول على تفاصيل امتحان محدد
     */
    public function show($code)
    {
        $exam = DB::table('exams')
            ->leftJoin('departments', 'exams.department_id', '=', 'departments.id')
            ->select(
                'exams.*',
                'departments.name as department_name'
            )
            ->where('exams.code', $code)
            ->first();
        
        if (!$exam) {
            return response()->json([
                'status' => false,
                'message' => 'لم يتم العثور على الامتحان'
            ], 404);
        }
        
        // إضافة معلومات إضافية للامتحان
        $exam->questions_count = $this->getQuestionsCount($exam);
        $exam->taken_count = DB::table('exam_records')
            ->where('exam_id', $exam->id)
            ->count();
        
        return response()->json([
            'status' => true,
            'exam' => $exam
        ]);
    }
    
    /**
     * إنشاء امتحان جديد
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|string|in:اختيارات,صح و خطأ',
            'language' => 'required|string|in:عربي,انجليزي',
            'level' => 'required|string',
            'department_id' => 'required|integer|exists:departments,id',
            'exam_data' => 'required|json',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // التحقق من صحة بيانات الامتحان
        $examData = json_decode($request->exam_data, true);
        if (!is_array($examData) || empty($examData)) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات الامتحان غير صالحة'
            ], 422);
        }
        
        // إنشاء امتحان جديد
        $code = Str::random(8);
        $examId = DB::table('exams')->insertGetId([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'language' => $request->language,
            'level' => $request->level,
            'department_id' => $request->department_id,
            'created_by' => Auth::id(),
            'exam_data' => $request->exam_data,
            'code' => $code,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // جلب بيانات الامتحان مع اسم القسم
        $exam = DB::table('exams')
            ->leftJoin('departments', 'exams.department_id', '=', 'departments.id')
            ->select(
                'exams.*',
                'departments.name as department_name'
            )
            ->where('exams.id', $examId)
            ->first();
        
        $exam->questions_count = $this->getQuestionsCount($exam);
        $exam->taken_count = 0;
        
        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء الامتحان بنجاح',
            'exam' => $exam
        ], 201);
    }
    
    /**
     * تحديث امتحان
     */
    public function update(Request $request, $code)
    {
        $exam = DB::table('exams')->where('code', $code)->first();
        
        if (!$exam) {
            return response()->json([
                'status' => false,
                'message' => 'لم يتم العثور على الامتحان'
            ], 404);
        }
        
        // التحقق من أن المستخدم هو منشئ الامتحان
        if ($exam->created_by != Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بتحديث هذا الامتحان'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string',
            'type' => 'string|in:اختيارات,صح و خطأ',
            'language' => 'string|in:عربي,انجليزي',
            'level' => 'string',
            'department_id' => 'integer|exists:departments,id',
            'exam_data' => 'json',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // تحضير بيانات التحديث
        $updateData = ['updated_at' => now()];
        
        if ($request->has('name')) {
            $updateData['name'] = $request->name;
        }
        
        if ($request->has('description')) {
            $updateData['description'] = $request->description;
        }
        
        if ($request->has('type')) {
            $updateData['type'] = $request->type;
        }
        
        if ($request->has('language')) {
            $updateData['language'] = $request->language;
        }
        
        if ($request->has('level')) {
            $updateData['level'] = $request->level;
        }
        
        if ($request->has('department_id')) {
            $updateData['department_id'] = $request->department_id;
        }
        
        if ($request->has('exam_data')) {
            // التحقق من صحة بيانات الامتحان
            $examData = json_decode($request->exam_data, true);
            if (!is_array($examData) || empty($examData)) {
                return response()->json([
                    'status' => false,
                    'message' => 'بيانات الامتحان غير صالحة'
                ], 422);
            }
            
            $updateData['exam_data'] = $request->exam_data;
        }
        
        // تنفيذ التحديث
        DB::table('exams')
            ->where('id', $exam->id)
            ->update($updateData);
        
        // جلب بيانات الامتحان المحدثة مع اسم القسم
        $updatedExam = DB::table('exams')
            ->leftJoin('departments', 'exams.department_id', '=', 'departments.id')
            ->select(
                'exams.*',
                'departments.name as department_name'
            )
            ->where('exams.id', $exam->id)
            ->first();
        
        $updatedExam->questions_count = $this->getQuestionsCount($updatedExam);
        $updatedExam->taken_count = DB::table('exam_records')
            ->where('exam_id', $updatedExam->id)
            ->count();
        
        return response()->json([
            'status' => true,
            'message' => 'تم تحديث الامتحان بنجاح',
            'exam' => $updatedExam
        ]);
    }
    
    /**
     * حذف امتحان
     */
    public function destroy($code)
    {
        $exam = DB::table('exams')->where('code', $code)->first();
        
        if (!$exam) {
            return response()->json([
                'status' => false,
                'message' => 'لم يتم العثور على الامتحان'
            ], 404);
        }
        
        // التحقق من أن المستخدم هو منشئ الامتحان
        if ($exam->created_by != Auth::id()) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بحذف هذا الامتحان'
            ], 403);
        }
        
        // حذف الامتحان
        DB::table('exams')->where('id', $exam->id)->delete();
        
        return response()->json([
            'status' => true,
            'message' => 'تم حذف الامتحان بنجاح'
        ]);
    }
    
    /**
     * تقديم إجابات الامتحان
     */
    public function submitExam(Request $request, $code)
    {
        // التحقق من المصادقة ووجود المستخدم
      
        $exam = DB::table('exams')
            ->where('code', $code)
            ->first();
            
        if (!$exam) {
            return response()->json([
                'status' => false,
                'message' => 'لم يتم العثور على الامتحان'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'score' => 'required|numeric|min:0|max:100',
            'correct' => 'required|integer|min:0',
            'wrong' => 'required|integer|min:0',
            'answers' => 'required|json',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // التحقق من صحة الإجابات
        $answers = json_decode($request->answers, true);
        if (!is_array($answers)) {
            return response()->json([
                'status' => false,
                'message' => 'الإجابات غير صالحة'
            ], 422);
        }
        
        // إنشاء سجل امتحان جديد مع student_id من المستخدم المصادق عليه
        $recordId = DB::table('exam_records')->insertGetId([
            'exam_id' => $exam->id,
            'student_id' => $request->student_id,
            'score' => (float)$request->score, // تأكيد التحويل إلى float
            'correct' => $request->correct,
            'wrong' => $request->wrong,
            'answers' => $request->answers,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $record = DB::table('exam_records')->where('id', $recordId)->first();
        
        return response()->json([
            'status' => true,
            'message' => 'تم تقديم الامتحان بنجاح',
            'record' => $record,
            'exam' => $exam
        ]);
    }
    
    /**
     * الحصول على نتيجة امتحان
     */
    public function getExamResult($code)
    {
        $exam = DB::table('exams')
            ->leftJoin('departments', 'exams.department_id', '=', 'departments.id')
            ->select(
                'exams.*',
                'departments.name as department_name'
            )
            ->where('exams.code', $code)
            ->first();
        
        if (!$exam) {
            return response()->json([
                'status' => false,
                'message' => 'لم يتم العثور على الامتحان'
            ], 404);
        }
        
        // الحصول على آخر سجل للمستخدم لهذا الامتحان
        $record = DB::table('exam_records')
            ->where('exam_id', $exam->id)
            ->where('student_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->first();
        
        if (!$record) {
            return response()->json([
                'status' => false,
                'message' => 'لم يتم العثور على نتيجة لهذا الامتحان'
            ], 404);
        }
        
        return response()->json([
            'status' => true,
            'record' => $record,
            'exam' => $exam
        ]);
    }
    
    /**
     * الحصول على عدد أسئلة الامتحان
     */
    private function getQuestionsCount($exam)
    {
        $examData = json_decode($exam->exam_data, true);
        return is_array($examData) ? count($examData) : 0;
    }
}