<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\University;
use App\Models\College;
use App\Models\Building;
use App\Models\Department;
use App\Models\Course;

class UniversityController extends Controller
{
    /**
     * الحصول على قائمة الجامعات
     */
    public function getUniversities()
    {
        $universities = University::all();
        
        return response()->json([
            'status' => 'success',
            'data' => $universities
        ]);
    }
    
    /**
     * الحصول على تفاصيل جامعة محددة
     */
    public function getUniversityDetails($id)
    {
        $university = University::with('colleges')->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $university
        ]);
    }
    
    /**
     * الحصول على قائمة الكليات لجامعة محددة
     */
    public function getColleges($universityId)
    {
        $colleges = College::where('university_id', $universityId)->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $colleges
        ]);
    }
    
    /**
     * الحصول على تفاصيل كلية محددة
     */
    public function getCollegeDetails($id)
    {
        $college = College::with(['departments', 'buildings'])->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $college
        ]);
    }
    
    /**
     * الحصول على قائمة المباني
     */
    public function getBuildings()
    {
        $buildings = Building::all();
        
        return response()->json([
            'status' => 'success',
            'data' => $buildings
        ]);
    }
    
    /**
     * الحصول على تفاصيل مبنى محدد
     */
    public function getBuildingDetails($id)
    {
        $building = Building::findOrFail($id);
        
        // الحصول على الكليات المرتبطة بهذا المبنى
        $colleges = $building->colleges;
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'building' => $building,
                'colleges' => $colleges
            ]
        ]);
    }
    
    /**
     * الحصول على قائمة الأقسام لكلية محددة
     */
    public function getDepartments($collegeId)
    {
        $departments = Department::where('college_id', $collegeId)->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $departments
        ]);
    }
    
    /**
     * الحصول على تفاصيل قسم محدد
     */
    public function getDepartmentDetails($id)
    {
        $department = Department::with('courses')->findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $department
        ]);
    }
    
    /**
     * الحصول على قائمة المقررات لقسم محدد
     */
    public function getCourses($departmentId, Request $request)
    {
        $query = Course::where('department_id', $departmentId);
        
        // تصفية حسب المستوى إذا تم تحديده
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }
        
        // تصفية حسب الفصل الدراسي إذا تم تحديده
        if ($request->has('term')) {
            $query->where('term', $request->term);
        }
        
        $courses = $query->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $courses
        ]);
    }
    
    /**
     * الحصول على تفاصيل مقرر محدد
     */
    public function getCourseDetails($id)
    {
        $course = Course::findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'data' => $course
        ]);
    }
    
    /**
     * البحث في الجامعات والكليات والأقسام والمباني
     */
    public function search(Request $request)
    {
        $query = $request->query('q');
        
        if (!$query) {
            return response()->json([
                'status' => 'error',
                'message' => 'يجب تحديد كلمة البحث'
            ], 400);
        }
        
        // البحث في الجامعات
        $universities = University::where('name', 'like', "%{$query}%")->get();
        
        // البحث في الكليات
        $colleges = College::where('name', 'like', "%{$query}%")->get();
        
        // البحث في الأقسام
        $departments = Department::where('name', 'like', "%{$query}%")->get();
        
        // البحث في المباني
        $buildings = Building::where('name', 'like', "%{$query}%")
            ->orWhere('location', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'universities' => $universities,
                'colleges' => $colleges,
                'departments' => $departments,
                'buildings' => $buildings
            ]
        ]);
    }
}
