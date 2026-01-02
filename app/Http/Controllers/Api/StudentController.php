<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Get student by ID
     */
    public function getStudentById(Request $request)
    {
        try {
            $request->validate([
                'student_id' => 'required|numeric'
            ]);
    
            $studentId = $request->input('student_id');
            
            
            $student = Student::with(['user', 'department'])
            ->where('id', $studentId)
            ->first();
            
            if (!$student) {
                $anyStudentExists = Student::exists();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found',
                    'details' => [
                        'requested_id' => $studentId,
                        'any_student_exists' => $anyStudentExists,
                        'first_3_students' => $anyStudentExists ? Student::take(3)->get(['id', 'full_name']) : null
                    ]
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $this->formatStudentData($student)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في الخادم',
                'error' => $e->getMessage(),
                'trace' => env('APP_DEBUG') ? $e->getTrace() : null
            ], 500);
        }
    }
    /**
     * Get current authenticated student
     */
    public function getCurrentStudent()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // البحث عن الطالب المرتبط بالمستخدم الحالي
            $student = Student::with(['user', 'department'])
                ->where('user_id', $user->id)
                ->first();
            
            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student data not found for this user'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $this->formatStudentData($student)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch student data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format student data for response
     */
    private function formatStudentData($student)
    {
        $userData = null;
        
        // تحقق إذا كان user موجوداً وغير فارغ
        if (isset($student->user) && $student->user instanceof \Illuminate\Database\Eloquent\Collection && $student->user->isNotEmpty()) {
            $firstUser = $student->user->first();
            $userData = [
                'id' => $firstUser->id,
                'name' => $firstUser->name,
                'email' => $firstUser->email,
                'phone_number' => $firstUser->phone_number,
                'image_url' => $firstUser->image_url,
                'status' => $firstUser->status,
                'last_login' => $firstUser->last_login
            ];
        }
    
        return [
            'id' => $student->id,
            'full_name' => $student->full_name,
            'gender' => $student->gender,
            'address' => $student->address,
            'date_of_birth' => $student->date_of_birth,
            'enrollment_year' => $student->enrollment_year,
            'level' => $student->level,
            'department' => $student->department ? [
                'id' => $student->department->id,
                'name' => $student->department->name
            ] : null,
            'user' => $userData, // سيكون null إذا لم يكن هناك user
            'created_at' => $student->created_at,
            'updated_at' => $student->updated_at
        ];
    } /**
 * التحقق من وجود الطالب في قاعدة البيانات
 */
public function checkStudentExistence($id)
{
    try {
        $exists = Student::where('id', $id)->exists();
        
        return response()->json([
            'success' => true,
            'exists' => $exists,
            'message' => $exists ? 'الطالب موجود' : 'الطالب غير موجود'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء التحقق',
            'error' => $e->getMessage()
        ], 500);
    }
}
}