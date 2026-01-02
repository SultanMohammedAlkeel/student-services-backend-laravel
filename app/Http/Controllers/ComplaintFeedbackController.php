<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ComplaintFeedback;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ComplaintFeedbackController extends Controller
{
    /**
     * Store a newly created complaint or feedback in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:2000',
                'type' => 'required|in:complaint,feedback',
            ], [
                'title.required' => 'العنوان مطلوب',
                'title.string' => 'العنوان يجب أن يكون نص',
                'title.max' => 'العنوان لا يجب أن يتجاوز 255 حرف',
                'description.required' => 'الوصف مطلوب',
                'description.string' => 'الوصف يجب أن يكون نص',
                'description.max' => 'الوصف لا يجب أن يتجاوز 2000 حرف',
                'type.required' => 'نوع الرسالة مطلوب',
                'type.in' => 'نوع الرسالة يجب أن يكون شكوى أو ملاحظة',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get the authenticated user
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير مصرح له'
                ], 401);
            }

            // Create the complaint/feedback
            $complaintFeedback = ComplaintFeedback::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
                'type' => $request->type,
                'status' => 'new'
            ]);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => $request->type === 'complaint' 
                    ? 'تم إرسال الشكوى بنجاح. سيتم التواصل معك عبر البريد الإلكتروني قريباً.'
                    : 'تم إرسال الملاحظة بنجاح. شكراً لك على مساهمتك في تحسين التطبيق.',
                'data' => [
                    'id' => $complaintFeedback->id,
                    'title' => $complaintFeedback->title,
                    'type' => $complaintFeedback->type,
                    'formatted_type' => $complaintFeedback->formatted_type,
                    'status' => $complaintFeedback->status,
                    'formatted_status' => $complaintFeedback->formatted_status,
                    'created_at' => $complaintFeedback->created_at->format('Y-m-d H:i:s'),
                ]
            ], 201);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error creating complaint/feedback: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' =>$e->getMessage() ?: 'حدث خطأ أثناء إرسال الشكوى أو الملاحظة'
            ], 500);
        }
    }

    /**
     * Get user's complaints and feedback (optional - for future use)
     */
    public function getUserComplaintsFeedback(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير مصرح له'
                ], 401);
            }

            $complaintsFeedback = ComplaintFeedback::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'description' => $item->description,
                        'type' => $item->type,
                        'formatted_type' => $item->formatted_type,
                        'status' => $item->status,
                        'formatted_status' => $item->formatted_status,
                        'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'تم جلب البيانات بنجاح',
                'data' => $complaintsFeedback
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching user complaints/feedback: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات'
            ], 500);
        }
    }
}

