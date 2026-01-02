<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Interaction;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PostController extends Controller
{
    /**
     * الحصول على جميع المنشورات
     */
    public function getPosts(Request $request)
    {
        try {
            $user = Auth::user();
            $offset = $request->input('offset', 0);
            $lastId = $request->input('last_id');
            
            $query = Post::with(['user' => function($query) {
                    $query->select('id', 'name', 'image_url as image');
                }])
                ->where('deleted', 0)
                ->orderBy('id', 'desc');
            
            if ($lastId) {
                $query->where('id', '<', $lastId);
            } else if ($offset) {
                $query->skip($offset);
            }
            
            $posts = $query->take(10)->get();
            
            // تنسيق البيانات لتتوافق مع النموذج
            $formattedPosts = $posts->map(function($post) use ($user) {
                $interaction = Interaction::where('user_id', $user->id)
                    ->where('post_id', $post->id)
                    ->first();
                
                return [
                    'id' => $post->id,
                    'sender_id' => $post->sender_id,
                    'content' => $post->content,
                    'file_url' => $post->file_url,
                    'file_type' => $post->file_type,
                    'file_size' => $post->file_size,
                    'views_count' => $post->views_count,
                    'likes_count' => $post->likes_count,
                    'comments_count' => $post->comments_count,
                    'deleted' => $post->deleted,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'user_name' => $post->user->name ?? 'مستخدم',
                    'user_image' => $post->user->image ?? 'images/profiles/default.png',
                    'is_liked' => $interaction && $interaction->like ? 1 : 0,
                    'is_saved' => $interaction && $interaction->save ? 1 : 0,
                    //'files' => $this->formatPostFiles($post)
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedPosts
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في الحصول على المنشورات: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المنشورات: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * الحصول على منشورات الكلية
     */
    public function getCollegePosts(Request $request)
    {
        try {
            $user = Auth::user();
            $offset = $request->input('offset', 0);
            $lastId = $request->input('last_id');
            
            $student = DB::table('students')->where('id', $user->user_id)->first();
            
            if (!$student || !$student->department_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على معلومات القسم للمستخدم'
                ], 400);
            }
            
            $departmentId = $student->department_id;
            
            $query = Post::with(['user' => function($query) {
                    $query->select('id', 'name', 'image_url as image');
                }])
                ->whereHas('user.student', function($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })
                ->where('deleted', 0)
                ->orderBy('id', 'desc');
            
            if ($lastId) {
                $query->where('id', '<', $lastId);
            } else if ($offset) {
                $query->skip($offset);
            }
            
            $posts = $query->take(10)->get();
            
            $postIds = $posts->pluck('id')->toArray();
            $interactions = Interaction::where('user_id', $user->id)
                ->whereIn('post_id', $postIds)
                ->get()
                ->keyBy('post_id');
            
            $formattedPosts = $posts->map(function($post) use ($interactions) {
                $interaction = $interactions[$post->id] ?? null;
                
                return [
                    'id' => $post->id,
                    'sender_id' => $post->sender_id,
                    'content' => $post->content,
                    'file_url' => $post->file_url,
                    'file_type' => $post->file_type,
                    'file_size' => $post->file_size,
                    'views_count' => $post->views_count,
                    'likes_count' => $post->likes_count,
                    'comments_count' => $post->comments_count,
                    'deleted' => $post->deleted,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'user_name' => $post->user->name ?? 'مستخدم',
                    'user_image' => $post->user->image ?? 'images/profiles/default.png',
                    'is_liked' => $interaction && $interaction->like ? 1 : 0,
                    'is_saved' => $interaction && $interaction->save ? 1 : 0,
                    'files' => $this->formatPostFiles($post)
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedPosts
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في الحصول على منشورات القسم: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب منشورات القسم: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * الحصول على المنشورات المحفوظة
     */
    public function getSavedPosts(Request $request)
    {
        try {
            $user = Auth::user();
            $offset = $request->input('offset', 0);
            $lastId = $request->input('last_id');
            
            $savedPostIds = Interaction::where('user_id', $user->id)
                ->where('save', 1)
                ->pluck('post_id');
            
            $query = Post::with(['user' => function($query) {
                    $query->select('id', 'name', 'image_url as image');
                }])
                ->whereIn('id', $savedPostIds)
                ->where('deleted', 0)
                ->orderBy('id', 'desc');
            
            if ($lastId) {
                $query->where('id', '<', $lastId);
            } else if ($offset) {
                $query->skip($offset);
            }
            
            $posts = $query->take(10)->get();
            
            $formattedPosts = $posts->map(function($post) use ($user) {
                return [
                    'id' => $post->id,
                    'sender_id' => $post->sender_id,
                    'content' => $post->content,
                    'file_url' => $post->file_url,
                    'file_type' => $post->file_type,
                    'file_size' => $post->file_size,
                    'views_count' => $post->views_count,
                    'likes_count' => $post->likes_count,
                    'comments_count' => $post->comments_count,
                    'deleted' => $post->deleted,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'user_name' => $post->user->name ?? 'مستخدم',
                    'user_image' => $post->user->image ?? 'images/profiles/default.png',
                    'is_liked' => Interaction::where('user_id', $user->id)
                        ->where('post_id', $post->id)
                        ->where('like', 1)
                        ->exists() ? 1 : 0,
                    'is_saved' => 1,
                    'files' => $this->formatPostFiles($post)
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedPosts
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في الحصول على المنشورات المحفوظة: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المنشورات المحفوظة: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * الحصول على منشورات المستخدم الحالي
     */
    public function getMyPosts(Request $request)
    {
        try {
            $user = Auth::user();
            $offset = $request->input('offset', 0);
            $lastId = $request->input('last_id');
            
            $query = Post::with(['user' => function($query) {
                    $query->select('id', 'name', 'image_url as image');
                }])
                ->where('sender_id', $user->id)
                ->where('deleted', 0)
                ->orderBy('id', 'desc');
            
            if ($lastId) {
                $query->where('id', '<', $lastId);
            } else if ($offset) {
                $query->skip($offset);
            }
            
            $posts = $query->take(10)->get();
            
            $formattedPosts = $posts->map(function($post) use ($user) {
                $interaction = Interaction::where('user_id', $user->id)
                    ->where('post_id', $post->id)
                    ->first();
                
                return [
                    'id' => $post->id,
                    'sender_id' => $post->sender_id,
                    'content' => $post->content,
                    'file_url' => $post->file_url,
                    'file_type' => $post->file_type,
                    'file_size' => $post->file_size,
                    'views_count' => $post->views_count,
                    'likes_count' => $post->likes_count,
                    'comments_count' => $post->comments_count,
                    'deleted' => $post->deleted,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'user_name' => $post->user->name ?? 'مستخدم',
                    'user_image' => $post->user->image ?? 'images/profiles/default.png',
                    'is_liked' => $interaction && $interaction->like ? 1 : 0,
                    'is_saved' => $interaction && $interaction->save ? 1 : 0,
                    'files' => $this->formatPostFiles($post)
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedPosts
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في الحصول على منشورات المستخدم: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب منشوراتك: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * البحث في المنشورات مع دعم الفلاتر المتقدمة
     */
    public function searchPosts(Request $request)
    {
        try {
            $user = Auth::user();
            $query = $request->input('q', '');
            $type = $request->input('type', 'all'); // all, content
            $limit = $request->input('limit', 20);
            $page = $request->input('page', 1);
            
            if (empty($query)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'total_pages' => 0,
                        'total_results' => 0,
                        'has_more' => false
                    ]
                ]);
            }
            
            // تقسيم استعلام البحث إلى كلمات مفتاحية
            $keywords = $this->prepareSearchKeywords($query);
            
            // بناء استعلام البحث الأساسي
            $postsQuery = Post::with(['user' => function($query) {
                    $query->select('id', 'name', 'image_url as image');
                }])
                ->where('deleted', 0);
            
            // تطبيق البحث حسب النوع
            if ($type == 'content') {
                // البحث في محتوى المنشورات فقط
                $postsQuery->where(function($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        if (strlen($keyword) >= 2) { // تجاهل الكلمات القصيرة جداً
                            $q->orWhere('content', 'LIKE', "%{$keyword}%");
                        }
                    }
                });
            } else {
                // البحث في المحتوى واسم المستخدم
                $postsQuery->where(function($q) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        if (strlen($keyword) >= 2) { // تجاهل الكلمات القصيرة جداً
                            $q->orWhere('content', 'LIKE', "%{$keyword}%")
                              ->orWhereHas('user', function($userQuery) use ($keyword) {
                                  $userQuery->where('name', 'LIKE', "%{$keyword}%")
                                           ->orWhere('code', 'LIKE', "%{$keyword}%");
                              });
                        }
                    }
                });
            }
            
            // تطبيق الفلاتر المتقدمة
            $this->applyAdvancedFilters($postsQuery, $request);
            
            // حساب إجمالي النتائج للصفحات
            $totalResults = $postsQuery->count();
            $totalPages = ceil($totalResults / $limit);
            
            // تطبيق الترتيب والصفحات
            $offset = ($page - 1) * $limit;
            $posts = $postsQuery->orderBy('created_at', 'desc')
                               ->skip($offset)
                               ->take($limit)
                               ->get();
            
            // إضافة معلومات التفاعل وحساب درجة الصلة
            $formattedPosts = $this->formatPostsWithRelevance($posts, $user, $keywords);
            
            // ترتيب النتائج حسب درجة الصلة
            $formattedPosts = $formattedPosts->sortByDesc('relevance_score')->values();
            
            return response()->json([
                'success' => true,
                'data' => $formattedPosts,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_results' => $totalResults,
                    'has_more' => $page < $totalPages
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في البحث عن المنشورات: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث عن المنشورات: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * البحث عن المستخدمين
     */
    public function searchUsers(Request $request)
    {
        try {
            $query = $request->input('q', '');
            $limit = $request->input('limit', 20);
            $page = $request->input('page', 1);
            
            if (empty($query)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'total_pages' => 0,
                        'total_results' => 0,
                        'has_more' => false
                    ]
                ]);
            }
            
            // تقسيم استعلام البحث إلى كلمات مفتاحية
            $keywords = $this->prepareSearchKeywords($query);
            
            // بناء استعلام البحث
            $usersQuery = User::select('id', 'name', 'image_url', 'code', 'email', 'phone_number', 'user');
            
            $usersQuery->where(function($q) use ($keywords) {
                foreach ($keywords as $keyword) {
                    if (strlen($keyword) >= 2) { // تجاهل الكلمات القصيرة جداً
                        $q->orWhere('name', 'LIKE', "%{$keyword}%")
                          ->orWhere('code', 'LIKE', "%{$keyword}%")
                          ->orWhere('email', 'LIKE', "%{$keyword}%");
                    }
                }
            });
            
            // حساب إجمالي النتائج للصفحات
            $totalResults = $usersQuery->count();
            $totalPages = ceil($totalResults / $limit);
            
            // تطبيق الصفحات
            $offset = ($page - 1) * $limit;
            $users = $usersQuery->orderBy('name')
                              ->skip($offset)
                              ->take($limit)
                              ->get();
            
            // حساب درجة الصلة لكل مستخدم
            $formattedUsers = $this->calculateUserRelevance($users, $keywords);
            
            // ترتيب النتائج حسب درجة الصلة
            $formattedUsers = $formattedUsers->sortByDesc('relevance_score')->values();
            
            return response()->json([
                'success' => true,
                'data' => $formattedUsers,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_results' => $totalResults,
                    'has_more' => $page < $totalPages
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في البحث عن المستخدمين: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث عن المستخدمين: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * جلب منشورات مستخدم معين
     */
    public function getUserPosts(Request $request, $userId)
    {
        try {
            $currentUser = Auth::user();
            $limit = $request->input('limit', 10);
            $page = $request->input('page', 1);
            $lastId = $request->input('last_id');
            
            // التحقق من وجود المستخدم
            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير موجود'
                ], 404);
            }
            
            // بناء استعلام جلب المنشورات
            $query = Post::with(['user' => function($query) {
                    $query->select('id', 'name', 'image_url as image');
                }])
                ->where('sender_id', $userId)
                ->where('deleted', 0)
                ->orderBy('created_at', 'desc');
            
            // دعم التحميل المتدرج
            if ($lastId) {
                $query->where('id', '<', $lastId);
            } else {
                $offset = ($page - 1) * $limit;
                $query->skip($offset);
            }
            
            $posts = $query->take($limit)->get();
            $totalResults = Post::where('sender_id', $userId)->where('deleted', 0)->count();
            $totalPages = ceil($totalResults / $limit);
            
            // إضافة معلومات التفاعل
            $formattedPosts = $posts->map(function($post) use ($currentUser) {
                $interaction = Interaction::where('user_id', $currentUser->id)
                    ->where('post_id', $post->id)
                    ->first();
                
                return [
                    'id' => $post->id,
                    'sender_id' => $post->sender_id,
                    'content' => $post->content,
                    'file_url' => $post->file_url,
                    'file_type' => $post->file_type,
                    'file_size' => $post->file_size,
                    'views_count' => $post->views_count,
                    'likes_count' => $post->likes_count,
                    'comments_count' => $post->comments_count,
                    'deleted' => $post->deleted,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'user_name' => $post->user->name ?? 'مستخدم',
                    'user_image' => $post->user->image ?? 'images/profiles/default.png',
                    'is_liked' => $interaction && $interaction->like ? 1 : 0,
                    'is_saved' => $interaction && $interaction->save ? 1 : 0,
                    'files' => $this->formatPostFiles($post)
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedPosts,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_results' => $totalResults,
                    'has_more' => $page < $totalPages
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في جلب منشورات المستخدم: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب منشورات المستخدم: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * 
     * إنشاء منشور جديد
     */
    public function createPost(Request $request)
    {
        try {
            $user = Auth::user();
            
            // التحقق من البيانات
            $request->validate([
                'content' => 'required_without:file_url|string',
                'file_url' => 'nullable|string',
                'file_type' => 'nullable|string',
                'file_size' => 'nullable|integer',
            ]);
            
            // تحديد نوع الملف بناءً على المدخلات
            $fileType = 'نص'; // القيمة الافتراضية
            if (!empty($request->file_url)) {
                if (!empty($request->file_type)) {
                    $fileType = $this->getArabicFileType($request->file_type);
                } else {
                    $fileType = $this->getArabicFileType($this->getFileType($request->file_url));
                }
            }
            
            // إنشاء المنشور
            $post = new Post();
            $post->sender_id = $user->id;
            $post->content = $request->content;
            $post->file_url = $request->file_url;
            $post->file_type = $fileType;
            $post->file_size = $request->file_size ?? 0;
            $post->save();
            
            // إضافة معلومات المستخدم للرد
            $post->user_name = $user->name;
            $post->user_image = $user->image;
            $post->is_liked = 0;
            $post->is_saved = 0;
            
            // تنسيق معلومات الملف
            $this->formatPostFiles($post);
            
            return response()->json([
                'success' => true,
                'data' => $post
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في إنشاء منشور: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء المنشور: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * رفع ملف للمنشور
     */
    public function uploadFile(Request $request)
    {
        try {
            // التحقق من وجود ملف
            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم تحديد ملف للرفع'
                ], 400);
            }
            
            $file = $request->file('file');
            $fileType = $request->input('type', $this->getFileType($file->getClientOriginalName()));
            
            // التحقق من صحة الملف
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'الملف غير صالح'
                ], 400);
            }
            
            // إنشاء اسم فريد للملف
            $fileName = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // تحديد المسار حسب نوع الملف
            $path = 'uploads/posts/';
            switch ($fileType) {
                case 'image':
                    $path .= 'images';
                    break;
                case 'video':
                    $path .= 'videos';
                    break;
                case 'audio':
                    $path .= 'audios';
                    break;
                default:
                    $path .= 'files';
                    break;
            }
            
            // رفع الملف
            $filePath = $file->storeAs($path, $fileName, 'public');
            
            // الحصول على حجم الملف
            $fileSize = $file->getSize();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'file_url' => 'storage/' . $filePath,
                    'file_type' => $fileType,
                    'file_size' => $fileSize,
                    'file_name' => $fileName
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في رفع الملف: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * رفع صورة للمنشور (للتوافق مع الواجهة القديمة)
     */
    public function uploadPostImage(Request $request)
    {
        return $this->uploadFile($request);
    }
    
    /**
     * حذف منشور
     */
    public function deletePost($id)
    {
        try {
            $user = Auth::user();
            $post = Post::find($id);
            
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'المنشور غير موجود'
                ], 404);
            }
            
            // التحقق من أن المستخدم هو صاحب المنشور أو مسؤول
            if ($post->sender_id != $user->id && $user->user != 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مصرح لك بحذف هذا المنشور'
                ], 403);
            }
            
            // حذف منطقي للمنشور
            $post->deleted = 1;
            $post->save();
            
            return response()->json([
                'success' => true,
                'message' => 'تم حذف المنشور بنجاح'
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في حذف المنشور: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المنشور: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * تبديل حالة الإعجاب بمنشور
     */
    public function toggleLike(Request $request)
    {
        try {
            $user = Auth::user();
            $postId = $request->input('post_id');
            
            // التحقق من وجود المنشور
            $post = Post::find($postId);
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'المنشور غير موجود'
                ], 404);
            }
            
            // البحث عن تفاعل موجود
            $interaction = Interaction::firstOrNew([
                'user_id' => $user->id,
                'post_id' => $postId
            ]);
            
            // تبديل حالة الإعجاب
            if ($interaction->exists) {
                if ($interaction->like) {
                    $interaction->like = 0;
                    $post->decrement('likes_count');
                } else {
                    $interaction->like = 1;
                    $post->increment('likes_count');
                }
            } else {
                $interaction->like = 1;
                $post->increment('likes_count');
            }
            
            $interaction->save();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'is_liked' => $interaction->like,
                    'likes_count' => $post->likes_count
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في تبديل حالة الإعجاب: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تبديل حالة الإعجاب: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * تبديل حالة حفظ منشور
     */
    public function toggleSave(Request $request)
    {
        try {
            $user = Auth::user();
            $postId = $request->input('post_id');
            
            // التحقق من وجود المنشور
            $post = Post::find($postId);
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'المنشور غير موجود'
                ], 404);
            }
            
            // البحث عن تفاعل موجود
            $interaction = Interaction::firstOrNew([
                'user_id' => $user->id,
                'post_id' => $postId
            ]);
            
            // تبديل حالة الحفظ
            if ($interaction->exists) {
                $interaction->save = $interaction->save ? 0 : 1;
            } else {
                $interaction->save = 1;
            }
            
            $interaction->save();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'is_saved' => $interaction->save
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في تبديل حالة الحفظ: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تبديل حالة الحفظ: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * تسجيل مشاهدة منشور
     */
    public function recordView(Request $request)
    {
        try {
            $user = Auth::user();
            $postId = $request->input('post_id');
            
            // التحقق من وجود المنشور
            $post = Post::find($postId);
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'المنشور غير موجود'
                ], 404);
            }
            
            // البحث عن تفاعل موجود
            $interaction = Interaction::firstOrNew([
                'user_id' => $user->id,
                'post_id' => $postId
            ]);
            
            // حفظ التفاعل إذا لم يكن موجوداً
            if (!$interaction->exists) {
                $interaction->save();
                $post->increment('views_count');
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'views_count' => $post->views_count
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في تسجيل المشاهدة: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل المشاهدة: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * الحصول على تعليقات منشور
     */
    public function getComments(Request $request)
    {
        try {
            $postId = $request->input('post_id');
            
            // التحقق من وجود المنشور
            $post = Post::find($postId);
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'المنشور غير موجود'
                ], 404);
            }
            
            // جلب التعليقات مع معلومات المستخدم
            $comments = Comment::with(['user' => function($query) {
                    $query->select('id', 'name', 'image_url as image');
                }])
                ->where('post_id', $postId)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // تنسيق البيانات
            $formattedComments = $comments->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'post_id' => $comment->post_id,
                    'user_id' => $comment->user_id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                    'user_name' => $comment->user->name ?? 'مستخدم',
                    'user_image' => $comment->user->image ?? 'images/profiles/default.png'
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedComments
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في جلب التعليقات: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب التعليقات: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * إضافة تعليق على منشور
     */
    public function addComment(Request $request)
    {
        try {
            $user = Auth::user();
            $postId = $request->input('post_id');
            $content = $request->input('comment');
            
            // التحقق من البيانات
            $request->validate([
                'post_id' => 'required|integer',
                'comment' => 'required|string'
            ]);
            
            // التحقق من وجود المنشور
            $post = Post::find($postId);
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'المنشور غير موجود'
                ], 404);
            }
            
            // إنشاء التعليق
            $comment = new Comment();
            $comment->post_id = $postId;
            $comment->user_id = $user->id;
            $comment->content = $content;
            $comment->save();
            
            // زيادة عدد التعليقات في المنشور
            $post->increment('comments_count');
            
            // إضافة معلومات المستخدم للرد
            $comment->user_name = $user->name;
            $comment->user_image = $user->image;
            
            return response()->json([
                'success' => true,
                'data' => $comment
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في إضافة تعليق: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة التعليق: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * تنسيق ملفات المنشور
     */
    private function formatPostFiles($post)
    {
        if (empty($post->file_url)) {
            return [];
        }
        
        return [
            [
                'url' => $post->file_url,
                'type' => $post->file_type,
                'size' => $post->file_size
            ]
        ];
    }
    
    /**
     * تحديد نوع الملف بناءً على اسم الملف
     */
    private function getFileType($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'flv', 'wmv'];
        $audioExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'flac'];
        
        if (in_array($extension, $imageExtensions)) {
            return 'image';
        } elseif (in_array($extension, $videoExtensions)) {
            return 'video';
        } elseif (in_array($extension, $audioExtensions)) {
            return 'audio';
        } else {
            return 'file';
        }
    }
    
    /**
     * تحويل نوع الملف إلى اللغة العربية
     */
    private function getArabicFileType($type)
    {
        switch ($type) {
            case 'image':
                return 'صورة';
            case 'video':
                return 'فيديو';
            case 'audio':
                return 'صوت';
            case 'file':
                return 'ملف';
            default:
                return 'نص';
        }
    }
    
    /**
     * تحضير كلمات البحث
     */
    private function prepareSearchKeywords($query)
    {
        // تقسيم النص إلى كلمات
        $keywords = preg_split('/\s+/', trim($query));
        
        // إزالة الكلمات الشائعة
        $stopWords = ['في', 'من', 'على', 'إلى', 'عن', 'مع', 'هذا', 'هذه', 'ذلك', 'تلك', 'هو', 'هي', 'أنا', 'نحن', 'أنت', 'أنتم'];
        $keywords = array_diff($keywords, $stopWords);
        
        // تنظيف الكلمات
        $keywords = array_map(function($keyword) {
            // إزالة علامات الترقيم
            $keyword = preg_replace('/[^\p{L}\p{N}\s]/u', '', $keyword);
            return trim($keyword);
        }, $keywords);
        
        // إزالة الكلمات الفارغة
        $keywords = array_filter($keywords, function($keyword) {
            return !empty($keyword);
        });
        
        return array_values($keywords);
    }
    
    /**
     * تطبيق الفلاتر المتقدمة على استعلام البحث
     */
    private function applyAdvancedFilters($query, $request)
    {
        // فلتر التاريخ
        if ($request->has('start_date') && !empty($request->start_date)) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }
        
        if ($request->has('end_date') && !empty($request->end_date)) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }
        
        // فلتر نوع الملف
        if ($request->has('file_type') && !empty($request->file_type) && $request->file_type != 'الكل') {
            $query->where('file_type', $request->file_type);
        }
        
        // فلتر عدد الإعجابات
        if ($request->has('min_likes') && is_numeric($request->min_likes)) {
            $query->where('likes_count', '>=', $request->min_likes);
        }
        
        if ($request->has('max_likes') && is_numeric($request->max_likes)) {
            $query->where('likes_count', '<=', $request->max_likes);
        }
        
        return $query;
    }
    
    /**
     * تنسيق المنشورات مع حساب درجة الصلة
     */
    private function formatPostsWithRelevance($posts, $user, $keywords)
    {
        return $posts->map(function($post) use ($user, $keywords) {
            $interaction = Interaction::where('user_id', $user->id)
                ->where('post_id', $post->id)
                ->first();
            
            // حساب درجة الصلة
            $relevanceScore = $this->calculatePostRelevance($post, $keywords);
            
            return [
                'id' => $post->id,
                'sender_id' => $post->sender_id,
                'content' => $post->content,
                'file_url' => $post->file_url,
                'file_type' => $post->file_type,
                'file_size' => $post->file_size,
                'views_count' => $post->views_count,
                'likes_count' => $post->likes_count,
                'comments_count' => $post->comments_count,
                'deleted' => $post->deleted,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
                'user_name' => $post->user->name ?? 'مستخدم',
                'user_image' => $post->user->image ?? 'images/profiles/default.png',
                'is_liked' => $interaction && $interaction->like ? 1 : 0,
                'is_saved' => $interaction && $interaction->save ? 1 : 0,
                'files' => $this->formatPostFiles($post),
                'relevance_score' => $relevanceScore
            ];
        });
    }
    
    /**
     * حساب درجة صلة المنشور بكلمات البحث
     */
    private function calculatePostRelevance($post, $keywords)
    {
        $score = 0;
        $content = mb_strtolower($post->content);
        $userName = mb_strtolower($post->user->name ?? '');
        
        // حساب درجة التطابق
        foreach ($keywords as $keyword) {
            if (mb_strlen($keyword) < 2) continue; // تجاهل الكلمات القصيرة جداً
            
            // تطابق في المحتوى
            if (mb_strpos($content, $keyword) !== false) {
                $score += 2;
                // تطابق في بداية المحتوى
                if (mb_strpos($content, $keyword) === 0) {
                    $score += 1;
                }
            }
            
            // تطابق في اسم المستخدم
            if (mb_strpos($userName, $keyword) !== false) {
                $score += 3;
            }
        }
        
        // إضافة وزن للمنشورات الأحدث
        $daysOld = Carbon::now()->diffInDays($post->created_at);
        $score += max(0, 5 - min(5, $daysOld / 7)); // أقصى 5 نقاط للمنشورات الجديدة
        
        // إضافة وزن للمنشورات الأكثر تفاعلاً
        $score += min(5, $post->likes_count / 10); // أقصى 5 نقاط للإعجابات
        $score += min(3, $post->comments_count / 5); // أقصى 3 نقاط للتعليقات
        
        return $score;
    }
    
    /**
     * حساب درجة صلة المستخدم بكلمات البحث
     */
    private function calculateUserRelevance($users, $keywords)
    {
        return $users->map(function($user) use ($keywords) {
            $score = 0;
            $name = mb_strtolower($user->name);
            $code = mb_strtolower($user->code ?? '');
            $email = mb_strtolower($user->email ?? '');
            
            // حساب درجة التطابق
            foreach ($keywords as $keyword) {
                if (mb_strlen($keyword) < 2) continue; // تجاهل الكلمات القصيرة جداً
                
                // تطابق في الاسم
                if (mb_strpos($name, $keyword) !== false) {
                    $score += 5;
                    // تطابق كامل للاسم
                    if ($name === $keyword) {
                        $score += 10;
                    }
                    // تطابق في بداية الاسم
                    else if (mb_strpos($name, $keyword) === 0) {
                        $score += 3;
                    }
                }
                
                // تطابق في الرمز
                if (!empty($code) && mb_strpos($code, $keyword) !== false) {
                    $score += 4;
                    // تطابق كامل للرمز
                    if ($code === $keyword) {
                        $score += 8;
                    }
                }
                
                // تطابق في البريد الإلكتروني
                if (!empty($email) && mb_strpos($email, $keyword) !== false) {
                    $score += 3;
                }
            }
            
            $userData = $user->toArray();
            $userData['relevance_score'] = $score;
            
            return $userData;
        });
    }
}
