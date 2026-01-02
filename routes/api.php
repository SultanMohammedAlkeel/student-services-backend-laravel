<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UniversityController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\ChatApiController;
use App\Http\Controllers\ComplaintFeedbackController;
use App\Http\Controllers\Api\PasswordResetController;

Route::post('/teacher/login', [AuthController::class, 'login'])->name('login');



// Public Routes
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/verify-student', [UserController::class, 'AuthAccount']);

// Password Reset Routes (Public - لا تحتاج تسجيل دخول)
Route::prefix('password')->group(function () {
    Route::post('/send-reset-code', [PasswordResetController::class, 'sendResetCode']);
    Route::post('/verify-reset-code', [PasswordResetController::class, 'verifyResetCode']);
    Route::post('/reset', [PasswordResetController::class, 'resetPassword']);
    Route::post('/cancel-codes', [PasswordResetController::class, 'cancelResetCodes']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Routes Authentication
    Route::get('/user', [UserController::class, 'getCurrentUser']);
    Route::post('/logout', [UserController::class, 'logout']);

    // Routes Student Data
    Route::get('/students/me', [StudentController::class, 'getCurrentStudent']);
    Route::post('/students/get-by-id', [StudentController::class, 'getStudentById']);

    // Routes Profile Management - Updated
    Route::post('/profile/upload-image', [UserController::class, 'uploadProfileImage']);
    Route::post('/profile/update', [UserController::class, 'updateProfile']);
    Route::post('/profile/update-contact', [UserController::class, 'updateContactInfo']);
    Route::post('/profile/change-password', [UserController::class, 'changePassword']);
    
    // Legacy Profile Routes (for backward compatibility)
    Route::post('/profile/upload', [UserController::class, 'uploadProfileImage']);
    Route::get('/profile/image', [ProfileController::class, 'getImage']);
    Route::get('/profile', [ProfileController::class, 'getProfile']);

    // الحصول على المنشورات
    Route::get('/posts', [PostController::class, 'getPosts']);
    Route::get('/posts/college', [PostController::class, 'getCollegePosts']);
    Route::get('/posts/saved', [PostController::class, 'getSavedPosts']);
    Route::get('/posts/my', [PostController::class, 'getMyPosts']);
    
    // البحث في المنشورات والمستخدمين
    Route::get('/posts/search', [PostController::class, 'searchPosts']);
    Route::get('/users/search', [PostController::class, 'searchUsers']);
    Route::get('/posts/user/{id}', [PostController::class, 'getUserPosts']);
    
    // إنشاء وحذف المنشورات
    Route::post('/posts/create', [PostController::class, 'createPost']);
    Route::delete('/posts/{id}', [PostController::class, 'deletePost']);
    
    // رفع الملفات
    Route::post('/posts/upload-file', [PostController::class, 'uploadFile']);
    Route::post('/posts/upload-image', [PostController::class, 'uploadPostImage']); // للتوافق مع الواجهة القديمة
    
    // التفاعلات
    Route::post('/posts/like', [PostController::class, 'toggleLike']);
    Route::post('/posts/save', [PostController::class, 'toggleSave']);
    Route::post('/posts/view', [PostController::class, 'recordView']);
    
    // التعليقات
    Route::get('/posts/comments', [PostController::class, 'getComments']);
    Route::post('/posts/comment', [PostController::class, 'addComment']);
    
    // الجدول الدراسي
    Route::get('/student/schedule', [ScheduleController::class, 'getStudentSchedule']);
    Route::get('/student/schedule/today', [ScheduleController::class, 'getTodayLectures']);
    Route::get('/student/schedule/tomorrow', [ScheduleController::class, 'getTomorrowLectures']);

    
    // مسارات الشكاوى والملاحظات - Help & Support Module
    
            Route::post('/complaints-feedback', [ComplaintFeedbackController::class, 'store']);
            Route::get('/my-complaints-feedback', [ComplaintFeedbackController::class, 'getUserComplaintsFeedback']); // اختياري للمستقبل
});

// إضافة مسارات المكتبة الإلكترونية
Route::group(['prefix' => 'library', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/books', [LibraryController::class, 'getBooks']);
    Route::get('/categories', [LibraryController::class, 'getCategories']);
    Route::get('/book-infos', [LibraryController::class, 'getBookInfos']);
    Route::get('/search', [LibraryController::class, 'searchBooks']);
    Route::get('/most-viewed', [LibraryController::class, 'getMostViewedBooks']);
    Route::get('/most-downloaded', [LibraryController::class, 'getMostDownloadedBooks']);
    Route::post('/books/addBook', [LibraryController::class, 'addBook']);
    Route::put('/books/{id}', [LibraryController::class, 'updateBook']);
    Route::delete('/books/{id}', [LibraryController::class, 'deleteBook']);
    Route::post('/books/{id}/like', [LibraryController::class, 'toggleLikeBook']);
    Route::post('/books/{id}/save', [LibraryController::class, 'toggleSaveBook']);
    Route::post('/books/{id}/download', [LibraryController::class, 'downloadBook']);
    Route::post('/books/{id}/view', [LibraryController::class, 'viewBook']);
    Route::get('/categories-list', [LibraryController::class, 'getCategoriesOnly']);
});

// مسارات الامتحانات
Route::prefix('exams')->group(function () {
    // الحصول على قائمة الامتحانات مع تطبيق الفلتر
    Route::get('/', [ExamController::class, 'index']);
    
    // الحصول على الامتحانات الشائعة
    Route::get('/popular', [ExamController::class, 'getPopularExams']);
    
    // الحصول على أحدث الامتحانات
    Route::get('/recent', [ExamController::class, 'getRecentExams']);
    
    // الحصول على امتحانات المستخدم
    Route::get('/my-exams', [ExamController::class, 'getMyExams']);
    
    // إنشاء امتحان جديد
    Route::post('/', [ExamController::class, 'store']);
    
    // الحصول على تفاصيل امتحان محدد
    Route::get('/{code}', [ExamController::class, 'show']);
    
    // تحديث امتحان
    Route::put('/{code}', [ExamController::class, 'update']);
    
    // حذف امتحان
    Route::delete('/{code}', [ExamController::class, 'destroy']);
    
    // تقديم إجابات الامتحان
    Route::post('/{code}/submit', [ExamController::class, 'submitExam']);
    
    // الحصول على نتيجة امتحان
    Route::get('/results/{code}', [ExamController::class, 'getExamResult']);
});

// مسارات سجلات الحضور والغياب للطلاب
Route::middleware('auth:sanctum')->group(function () {
    // الحصول على سجلات الحضور للطالب المسجل دخوله
    Route::get('/student/attendance', [AttendanceController::class, 'getStudentAttendanceRecords']);
    
    // الحصول على تفاصيل حضور مقرر معين
    Route::get('/student/attendance/course/{courseId}', [AttendanceController::class, 'getCourseAttendanceDetails']);
    
    // الحصول على إحصائيات الحضور العامة للطالب
    Route::get('/student/attendance/statistics', [AttendanceController::class, 'getAttendanceStatistics']);
});


    // ==========================================
    // Chat Routes (المحادثات - موجودة مسبقاً)
    // ==========================================
Route::middleware('auth:sanctum')->group(function () {
    // Chat Endpoints
    Route::get('/chat', [ChatApiController::class, 'index']); // Get all chats
    Route::get('/chat/{id}/messages', [ChatApiController::class, 'messages']); // Get chat messages
    Route::post('/chat/send-messages', [ChatApiController::class, 'sendMessage']); // Send message
    Route::post('/chat/messages/{id}/read', [ChatApiController::class, 'markAsRead']); // Mark as read
    Route::delete('/chat/messages/{id}', [ChatApiController::class, 'deleteMessage']); // Delete message
    Route::get('/chat/download-file', [ChatApiController::class, 'downloadFile']);
    // إنشاء محادثة جديدة
    Route::post('/chat/create', [ChatApiController::class, 'createChat']);

    // جلب المستخدمين المتاحين لبدء محادثة
    Route::get('/chat/available-users', [ChatApiController::class, 'getAvailableUsers']);
    Route::post('/chat/add-contact', [ChatApiController::class, 'addContact']);

    // حظر/إلغاء حظر مستخدم
    Route::post('/chat/block-user', [ChatApiController::class, 'blockUser']);
    Route::post('/chat/unblock-user', [ChatApiController::class, 'unblockUser']);

});

