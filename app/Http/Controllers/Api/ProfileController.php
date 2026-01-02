<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function uploadImage(Request $request)
    {
        // التحقق من المصادقة
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        // التحقق من الصورة
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120' // 5MB كحد أقصى
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        DB::beginTransaction();
        try {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($user->image_url) {
                $oldPath = parse_url($user->image_url, PHP_URL_PATH);
                $oldPath = str_replace('/storage/', '', $oldPath);
                Storage::disk('public')->delete($oldPath);
            }
    
            // حفظ الصورة الجديدة
            $fileName = 'user_' . $user->id . '_' . time() . '.' . $request->file('image')->extension();
            $path = $request->file('image')->storeAs(
                'student_profile',
                $fileName,
                'public'
            );
    
            // تحديث رابط الصورة في جدول المستخدم
            $user->image_url = asset('storage/' . $path);
            $user->save();
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'image_url' => $user->image_url,
                'message' => 'تم رفع الصورة بنجاح'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Upload failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'فشل في رفع الصورة: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getImage(Request $request)
    {
        $user = $request->user();
        
        if (!$user || !$user->image_url) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد صور متاحة'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'image_url' => $user->image_url
        ]);
    }

    
    public function deleteProfileImage()
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user || !$user->image_path) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد صورة بروفايل'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // حذف الملف من التخزين
            Storage::disk('public')->delete($user->image_path);

            // تحديث بيانات المستخدم
            $user->update([
                'image_path' => null,
                'image_url' => null
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الصورة بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Image deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'فشل في حذف الصورة: ' . $e->getMessage()
            ], 500);
        }
    }
}