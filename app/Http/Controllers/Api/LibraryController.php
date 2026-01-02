<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Book_infos;
use App\Models\Category;
use App\Models\College;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LibraryController extends Controller
{
    /**
     * الحصول على قائمة الكتب
     */
    public function getBooks(Request $request)
    {
        $books = DB::table('books')
            ->leftJoin('categories', 'books.category_id', '=', 'categories.id')
            ->leftJoin('colleges', 'books.college_id', '=', 'colleges.id')
            ->leftJoin('departments', 'books.department_id', '=', 'departments.id')
            ->leftJoin('users', 'books.added_by', '=', 'users.id')
            ->where('books.is_active', 1)
            ->select(
                'books.*',
                'categories.name as category_name',
                'colleges.name as college_name',
                'departments.name as department_name',
                'users.name as added_by_name'
            )
            ->get()
            ->map(function ($book) {
                return $this->formatBookResponse($book);
            });

        return response()->json([
            'success' => true,
            'data' => $books
        ]);
    }

    /**
     * الحصول على قائمة التصنيفات
     */
    public function getCategories()
    {
        $categories = DB::table('categories')
            ->leftJoin('books', 'categories.id', '=', 'books.category_id')
            ->select('categories.id', 'categories.name', 'categories.created_at', 'categories.updated_at', DB::raw('COUNT(books.id) as books_count'))
            ->groupBy('categories.id', 'categories.name', 'categories.created_at', 'categories.updated_at')
            ->get();
    
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
    /**
     * الحصول على قائمه الاصناف للكتب
     */
    public function getCategoriesOnly(){
        $categories = DB::table('categories')
            ->get();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);

    }

    /**
     * الحصول على معلومات تفاعل المستخدم مع الكتب
     */
    public function getBookInfos()
    {
        $userId = Auth::id();
        $bookInfos = DB::table('book_infos')
            ->where('user_id', $userId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookInfos
        ]);
    }

    /**
     * البحث في الكتب
     */
    public function searchBooks(Request $request)
    {
        $query = $request->input('query', '');
        $fileType = $request->input('file_type', '');
        $bookType = $request->input('book_type', '');
        $collegeId = $request->input('college_id', 0);
        $departmentId = $request->input('department_id', 0);
        $level = $request->input('level', '');
        $categoryId = $request->input('category_id', 0);

        $books = DB::table('books')
            ->leftJoin('categories', 'books.category_id', '=', 'categories.id')
            ->leftJoin('colleges', 'books.college_id', '=', 'colleges.id')
            ->leftJoin('departments', 'books.department_id', '=', 'departments.id')
            ->leftJoin('users', 'books.added_by', '=', 'users.id')
            ->where('books.is_active', 1);

        if (!empty($query)) {
            $books->where(function ($q) use ($query) {
                $q->where('books.title', 'like', "%$query%")
                    ->orWhere('books.author', 'like', "%$query%")
                    ->orWhere('books.description', 'like', "%$query%");
            });
        }

        // تطبيق الفلاتر
        if (!empty($fileType)) {
            $books->where('books.file_type', $fileType);
        }

        if (!empty($bookType)) {
            $books->where('books.type', $bookType);
        }

        if ($collegeId > 0) {
            $books->where('books.college_id', $collegeId);
        }

        if ($departmentId > 0) {
            $books->where('books.department_id', $departmentId);
        }

        if (!empty($level)) {
            $books->where('books.level', $level);
        }

        if ($categoryId > 0) {
            $books->where('books.category_id', $categoryId);
        }

        $results = $books->select(
            'books.*',
            'categories.name as category_name',
            'colleges.name as college_name',
            'departments.name as department_name',
            'users.name as added_by_name'
        )
            ->get()
            ->map(function ($book) {
                return $this->formatBookResponse($book);
            });

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    /**
     * الحصول على الكتب الأكثر مشاهدة
     */
    public function getMostViewedBooks()
    {
        $books = DB::table('books')
            ->leftJoin('categories', 'books.category_id', '=', 'categories.id')
            ->leftJoin('colleges', 'books.college_id', '=', 'colleges.id')
            ->leftJoin('departments', 'books.department_id', '=', 'departments.id')
            ->leftJoin('users', 'books.added_by', '=', 'users.id')
            ->where('books.is_active', 1)
            ->orderBy('books.opens_count', 'desc')
            ->limit(10)
            ->select(
                'books.*',
                'categories.name as category_name',
                'colleges.name as college_name',
                'departments.name as department_name',
                'users.name as added_by_name'
            )
            ->get()
            ->map(function ($book) {
                return $this->formatBookResponse($book);
            });

        return response()->json([
            'success' => true,
            'data' => $books
        ]);
    }

    /**
     * الحصول على الكتب الأكثر تحميلاً
     */
    public function getMostDownloadedBooks()
    {
        $books = DB::table('books')
            ->leftJoin('categories', 'books.category_id', '=', 'categories.id')
            ->leftJoin('colleges', 'books.college_id', '=', 'colleges.id')
            ->leftJoin('departments', 'books.department_id', '=', 'departments.id')
            ->leftJoin('users', 'books.added_by', '=', 'users.id')
            ->where('books.is_active', 1)
            ->orderBy('books.download_count', 'desc')
            ->limit(10)
            ->select(
                'books.*',
                'categories.name as category_name',
                'colleges.name as college_name',
                'departments.name as department_name',
                'users.name as added_by_name'
            )
            ->get()
            ->map(function ($book) {
                return $this->formatBookResponse($book);
            });

        return response()->json([
            'success' => true,
            'data' => $books
        ]);
    }

    /**
     * إضافة كتاب جديد
     */
    public function addBook(Request $request)
    {
        // التحقق من البيانات
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|exists:categories,id',
            'college_id' => 'nullable|integer|exists:colleges,id',
            'department_id' => 'nullable|integer|exists:departments,id',
            'level' => 'nullable|string|max:50',
            'type' => 'required|string|in:عام,خاص,مشترك,محدد',
            'code' => 'nullable|string|max:50',
            'file' => 'required|file|max:20480', // الحد الأقصى 20 ميجابايت
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        // معالجة الملف
        $file = $request->file('file');
        $fileExtension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . Str::random(10) . '.' . $fileExtension;
        $filePath = 'books/' . $fileName;
        
        // تحديد نوع الملف
        $fileType = $this->getFileType($fileExtension);
        
        // حفظ الملف
        Storage::disk('public')->put($filePath, file_get_contents($file));
        $fileUrl = asset('storage/' . $filePath);
        $fileSize = $file->getSize();

        // إنشاء الكتاب
        $bookId = DB::table('books')->insertGetId([
            'title' => $request->input('title'),
            'author' => $request->input('author'),
            'description' => $request->input('description', ''),
            'file_url' => $fileUrl,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'category_id' => $request->input('category_id'),
            'college_id' => $request->input('college_id'),
            'department_id' => $request->input('department_id'),
            'level' => $request->input('level'),
            'type' => $request->input('type'),
            'added_by' => Auth::id(),
            'likes_count' => 0,
            'download_count' => 0,
            'opens_count' => 0,
            'save_count' => 0,
            'is_active' => 1,
            'code' => $request->input('code', Str::random(8)),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // الحصول على الكتاب المضاف
        $book = DB::table('books')
            ->leftJoin('categories', 'books.category_id', '=', 'categories.id')
            ->leftJoin('colleges', 'books.college_id', '=', 'colleges.id')
            ->leftJoin('departments', 'books.department_id', '=', 'departments.id')
            ->leftJoin('users', 'books.added_by', '=', 'users.id')
            ->where('books.id', $bookId)
            ->select(
                'books.*',
                'categories.name as category_name',
                'colleges.name as college_name',
                'departments.name as department_name',
                'users.name as added_by_name'
            )
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الكتاب بنجاح',
            'data' => $this->formatBookResponse($book)
        ]);
    }

    /**
     * تحديث كتاب
     */
    public function updateBook(Request $request, $id)
    {
        // التحقق من وجود الكتاب
        $book = DB::table('books')->where('id', $id)->first();
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'الكتاب غير موجود'
            ], 404);
        }

        // التحقق من صلاحية المستخدم
        if ($book->added_by != Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية لتحديث هذا الكتاب'
            ], 403);
        }

        // التحقق من البيانات
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer|exists:categories,id',
            'college_id' => 'nullable|integer|exists:colleges,id',
            'department_id' => 'nullable|integer|exists:departments,id',
            'level' => 'nullable|string|max:50',
            'type' => 'nullable|string|in:عام,خاص,مشترك,محدد',
            'code' => 'nullable|string|max:50',
            'file' => 'nullable|file|max:20480', // الحد الأقصى 20 ميجابايت
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'فشل التحقق من البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        // تحضير البيانات للتحديث
        $updateData = [];
        
        if ($request->has('title')) {
            $updateData['title'] = $request->input('title');
        }
        
        if ($request->has('author')) {
            $updateData['author'] = $request->input('author');
        }
        
        if ($request->has('description')) {
            $updateData['description'] = $request->input('description');
        }
        
        if ($request->has('category_id')) {
            $updateData['category_id'] = $request->input('category_id');
        }
        
        if ($request->has('college_id')) {
            $updateData['college_id'] = $request->input('college_id');
        }
        
        if ($request->has('department_id')) {
            $updateData['department_id'] = $request->input('department_id');
        }
        
        if ($request->has('level')) {
            $updateData['level'] = $request->input('level');
        }
        
        if ($request->has('type')) {
            $updateData['type'] = $request->input('type');
        }
        
        if ($request->has('code')) {
            $updateData['code'] = $request->input('code');
        }

        // معالجة الملف إذا تم تقديمه
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . Str::random(10) . '.' . $fileExtension;
            $filePath = 'books/' . $fileName;
            
            // تحديد نوع الملف
            $fileType = $this->getFileType($fileExtension);
            
            // حفظ الملف الجديد
            Storage::disk('public')->put($filePath, file_get_contents($file));
            $fileUrl = asset('storage/' . $filePath);
            $fileSize = $file->getSize();
            
            // حذف الملف القديم
            $oldFilePath = str_replace(asset('storage/'), '', $book->file_url);
            if (Storage::disk('public')->exists($oldFilePath)) {
                Storage::disk('public')->delete($oldFilePath);
            }
            
            $updateData['file_url'] = $fileUrl;
            $updateData['file_type'] = $fileType;
            $updateData['file_size'] = $fileSize;
        }
        
        $updateData['updated_at'] = now();
        
        // تحديث الكتاب
        DB::table('books')->where('id', $id)->update($updateData);
        
        // الحصول على الكتاب المحدث
        $updatedBook = DB::table('books')
            ->leftJoin('categories', 'books.category_id', '=', 'categories.id')
            ->leftJoin('colleges', 'books.college_id', '=', 'colleges.id')
            ->leftJoin('departments', 'books.department_id', '=', 'departments.id')
            ->leftJoin('users', 'books.added_by', '=', 'users.id')
            ->where('books.id', $id)
            ->select(
                'books.*',
                'categories.name as category_name',
                'colleges.name as college_name',
                'departments.name as department_name',
                'users.name as added_by_name'
            )
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الكتاب بنجاح',
            'data' => $this->formatBookResponse($updatedBook)
        ]);
    }

    /**
     * حذف كتاب
     */
    public function deleteBook(Request $request, $id)
    {
        // التحقق من وجود الكتاب
        $book = DB::table('books')->where('id', $id)->first();
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'الكتاب غير موجود'
            ], 404);
        }

        // التحقق من صلاحية المستخدم
        if ($book->added_by != Auth::id() && !Auth::user()->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية لحذف هذا الكتاب'
            ], 403);
        }

        // حذف الكتاب (تحديث حالة النشاط فقط)
        DB::table('books')->where('id', $id)->update([
            'is_active' => 0,
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الكتاب بنجاح'
        ]);
    }

    /**
     * الإعجاب بكتاب
     */
    public function toggleLikeBook(Request $request, $id)
    {
        $userId = Auth::id();

        return DB::transaction(function () use ($userId, $id) {
            $existingBookInfo = DB::table('book_infos')
                ->where('user_id', $userId)
                ->where('book_id', $id)
                ->first();

            if ($existingBookInfo) {
                if ($existingBookInfo->likes != 1) {
                    DB::table('book_infos')
                        ->where('id', $existingBookInfo->id)
                        ->update(['likes' => DB::raw('likes + 1')]);
                    DB::table('books')
                        ->where('id', $id)
                        ->update(['likes_count' => DB::raw('likes_count + 1')]);
                } else {
                    DB::table('book_infos')
                        ->where('id', $existingBookInfo->id)
                        ->update(['likes' => DB::raw('likes - 1')]);
                    DB::table('books')
                        ->where('id', $id)
                        ->update(['likes_count' => DB::raw('likes_count - 1')]);
                }
            } else {
                $bookInfoId = DB::table('book_infos')->insertGetId([
                    'book_id' => $id,
                    'user_id' => $userId,
                    'likes' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                DB::table('books')
                    ->where('id', $id)
                    ->update(['likes_count' => DB::raw('likes_count + 1')]);
                $existingBookInfo = DB::table('book_infos')->find($bookInfoId);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الإعجاب بنجاح',
                'data' => $existingBookInfo
            ]);
        });
    }

    /**
     * حفظ كتاب
     */
    public function toggleSaveBook(Request $request, $id)
    {
        $userId = Auth::id();

        return DB::transaction(function () use ($userId, $id) {
            $existingBookInfo = DB::table('book_infos')
                ->where('user_id', $userId)
                ->where('book_id', $id)
                ->first();

            if ($existingBookInfo) {
                if ($existingBookInfo->save != 1) {
                    DB::table('book_infos')
                        ->where('id', $existingBookInfo->id)
                        ->update(['save' => DB::raw('save + 1')]);
                    DB::table('books')
                        ->where('id', $id)
                        ->update(['save_count' => DB::raw('save_count + 1')]);
                } else {
                    DB::table('book_infos')
                        ->where('id', $existingBookInfo->id)
                        ->update(['save' => DB::raw('save - 1')]);
                    DB::table('books')
                        ->where('id', $id)
                        ->update(['save_count' => DB::raw('save_count - 1')]);
                }
            } else {
                $bookInfoId = DB::table('book_infos')->insertGetId([
                    'book_id' => $id,
                    'user_id' => $userId,
                    'save' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                DB::table('books')
                    ->where('id', $id)
                    ->update(['save_count' => DB::raw('save_count + 1')]);
                $existingBookInfo = DB::table('book_infos')->find($bookInfoId);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الحفظ بنجاح',
                'data' => $existingBookInfo
            ]);
        });
    }

    /**
     * تحميل كتاب
     */
    public function downloadBook(Request $request, $id)
    {
        $userId = Auth::id();

        return DB::transaction(function () use ($userId, $id) {
            $existingBookInfo = DB::table('book_infos')
                ->where('user_id', $userId)
                ->where('book_id', $id)
                ->first();

            if ($existingBookInfo) {
                DB::table('book_infos')
                    ->where('id', $existingBookInfo->id)
                    ->update(['downloads' => DB::raw('downloads + 1')]);
                DB::table('books')
                    ->where('id', $id)
                    ->update(['download_count' => DB::raw('download_count + 1')]);
            } else {
                $bookInfoId = DB::table('book_infos')->insertGetId([
                    'book_id' => $id,
                    'user_id' => $userId,
                    'downloads' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                DB::table('books')
                    ->where('id', $id)
                    ->update(['download_count' => DB::raw('download_count + 1')]);
                $existingBookInfo = DB::table('book_infos')->find($bookInfoId);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل تحميل الكتاب بنجاح',
                'data' => $existingBookInfo
            ]);
        });
    }

    /**
     * مشاهدة كتاب
     */
    public function viewBook(Request $request, $id)
    {
        $userId = Auth::id();

        return DB::transaction(function () use ($userId, $id) {
            $existingBookInfo = DB::table('book_infos')
                ->where('user_id', $userId)
                ->where('book_id', $id)
                ->first();

            if ($existingBookInfo) {
                DB::table('book_infos')
                    ->where('id', $existingBookInfo->id)
                    ->update(['opens_count' => DB::raw('opens_count + 1')]);
                DB::table('books')
                    ->where('id', $id)
                    ->update(['opens_count' => DB::raw('opens_count + 1')]);
            } else {
                $bookInfoId = DB::table('book_infos')->insertGetId([
                    'book_id' => $id,
                    'user_id' => $userId,
                    'opens_count' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                DB::table('books')
                    ->where('id', $id)
                    ->update(['opens_count' => DB::raw('opens_count + 1')]);
                $existingBookInfo = DB::table('book_infos')->find($bookInfoId);
            }

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل مشاهدة الكتاب بنجاح',
                'data' => $existingBookInfo
            ]);
        });
    }

    /**
     * تحديد نوع الملف بناءً على الامتداد
     */
    private function getFileType($extension)
    {
        $extension = strtolower($extension);
        
        switch ($extension) {
            case 'pdf':
                return 'PDF';
            case 'doc':
            case 'docx':
                return 'Microsoft Word';
            case 'xls':
            case 'xlsx':
                return 'Microsoft Excel';
            case 'ppt':
            case 'pptx':
                return 'PowerPoint';
            case 'txt':
                return 'Text Files';
            case 'php':
            case 'js':
            case 'html':
            case 'css':
            case 'java':
            case 'py':
            case 'c':
            case 'cpp':
                return 'Programming Files';
            case 'exe':
            case 'bat':
            case 'sh':
                return 'Executable Files';
            case 'db':
            case 'sql':
            case 'sqlite':
                return 'Database Files';
            default:
                return 'Other';
        }
    }

    /**
     * تنسيق استجابة الكتاب
     */
    private function formatBookResponse($book)
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author,
            'description' => $book->description,
            'file_url' => $book->file_url,
            'file_type' => $book->file_type,
            'file_size' => $book->file_size,
            'category_id' => $book->category_id,
            'category_name' => $book->category_name ?? null,
            'college_id' => $book->college_id,
            'college_name' => $book->college_name ?? null,
            'department_id' => $book->department_id,
            'department_name' => $book->department_name ?? null,
            'level' => $book->level,
            'type' => $book->type,
            'added_by' => $book->added_by,
            'added_by_name' => $book->added_by_name ?? null,
            'likes_count' => $book->likes_count,
            'download_count' => $book->download_count,
            'opens_count' => $book->opens_count,
            'save_count' => $book->save_count,
            'is_active' => $book->is_active,
            'code' => $book->code,
            'created_at' => $book->created_at,
            'updated_at' => $book->updated_at,
        ];
    }
}
