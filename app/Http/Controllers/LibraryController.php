<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Book_infos;
use App\Models\Category;
use App\Models\College;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LibraryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    var $fileIcons = [
            'PDF' => '<i class="fas fa-file-pdf" style="color: #e74c3c; font-size: 80px"></i>',
            'Microsoft Word' => '<i class="fas fa-file-word" style="color: #2b579a; font-size: 80px"></i>',
            'Microsoft Excel' => '<i class="fas fa-file-excel" style="color: #217346; font-size: 80px"></i>',
            'PowerPoint' => '<i class="fas fa-file-powerpoint" style="color: #d24726; font-size: 80px"></i>',
            'Text Files' => '<i class="fas fa-file-alt" style="color: #7f8c8d; font-size: 80px"></i>',
            'Programming Files' => '<i class="fas fa-file-code" style="color: #f39c12; font-size: 80px"></i>',
            'Executable Files' => '<i class="fas fa-file-archive" style="color: #8e44ad; font-size: 80px"></i>',
            'Database Files' => '<i class="fas fa-database" style="color: #3498db; font-size: 80px"></i>'
        ];
    public function index(Request $request)
    {
        $search = $request->input('search');
        if (!session('logged_in')) {
            return redirect('/welcome');
        }
        $categories = Category::all();
        $book_infos = Book_infos::all();
        $college = College::all();
        $department = Department::all(); 
        $types = ['عام', 'خاص', 'مشترك','محدد'];
        $levels = ['المستوى الاول','المستوى الثاني','المستوى الثالث','المستوى الرابع','المستوى الخامس','المستوى السادس','المستوى السابع'];
        $file_types = ['PDF ', 'Microsoft Word', 'Microsoft Excel', 'PowerPoint', 'Text Files', 'Programming Files', 'Executable Files', 'Database Files'];
              
        $books = Book::where('title', 'like', "%$search%")
        ->orWhere('author', 'like', "%$search%")
        ->orWhere('description', 'like', "%$search%")
        ->get();

        return view('library.library', [
            'categories' => $categories, 
            'book_infos' => $book_infos, 
            'books' => $books->where('is_active', 1), 
            'types' => $types,
            'colleges' => $college,
            'departments' => $department,
            'levels' => $levels,
            'file_types' => $file_types,
            'users' => User::all(),
            'fileIcons' => $this->fileIcons
        ]);
    } 
    public function search(Request $request)
    { 
        $search = $request->input('search');
        if (!session('logged_in')) {
            return redirect('/welcome');
        }
        $categories = Category::all();
        $book_infos = Book_infos::all();
        $college = College::all();
        $department = Department::all(); 
        $types = ['عام', 'خاص', 'مشترك','محدد'];
        $levels = ['المستوى الاول','المستوى الثاني','المستوى الثالث','المستوى الرابع','المستوى الخامس','المستوى السادس','المستوى السابع'];
        $file_types = ['PDF ', 'Microsoft Word', 'Microsoft Excel', 'PowerPoint', 'Text Files', 'Programming Files', 'Executable Files', 'Database Files'];
              
        $books = Book::where('title', 'like', "%$search%")
        ->orWhere('author', 'like', "%$search%")
        ->orWhere('description', 'like', "%$search%")
        ->get();

        return view('library.library', [
            'categories' => $categories, 
            'book_infos' => $book_infos, 
            'books' => $books->where('is_active', 1), 
            'types' => $types,
            'colleges' => $college,
            'departments' => $department,
            'levels' => $levels,
            'file_types' => $file_types,
            'users' => User::all()
        ]);
    }    
    public function getBook(Request $request)
    {
        $books = Book::all();
        if ($request->type) {
            // $books

            return $request;
        }
        $categories = Category::all();
        $book_infos = Book_infos::all();
        $college = College::all();
        $department = Department::all();
        $users = User::all();
        

        $types = ['PDF ', 'Microsoft Word', 'Microsoft Excel', 'PowerPoint', 'Text Files', 'Programming Files', 'Executable Files', 'Database Files'];
        return view('library.library', [
            'categories' => $categories, 
            'book_infos' => $book_infos, 
            'books' => $books->where('is_active', 1),  
            'types' => $types,
            'colleges' => $college,
            'departments' => $department,
            'users' => $users,
            'fileIcons' => $this->fileIcons,
            'file_types' => $types,
            'levels' => ['المستوى الاول','المستوى الثاني','المستوى الثالث','المستوى الرابع','المستوى الخامس','المستوى السادس','المستوى السابع']
            
        ]);
    }    

    public function StopBook(Request $request)
    {
        $book = Book::find($request->id);
        if ($book) {
            $book_status = $book->is_active;
            $book->is_active = $book_status == 1 ? 0 : 1;
            $book->save();
        }
        return redirect()->back();
    }

    public function BookManagement()
    {
        if (!session('logged_in')) {
            return redirect('/welcome');
        }
        $categories = Category::all();
        $book_infos = Book_infos::all();
        $college = College::all();
        $department = Department::all();

        $types = ['PDF ', 'Microsoft Word', 'Microsoft Excel', 'PowerPoint', 'Text Files', 'Programming Files', 'Executable Files', 'Database Files'];
        
        return view('admins.management.books', [
            'categories' => $categories, 
            'book_infos' => $book_infos, 
            'books' => Book::all(), 
            'types' => $types,
            'colleges' => $college,
            'departments' => $department,
            'users' => User::all()
        ]);
    }
    public function GetMyBook()
    {
        if (!session('logged_in')) {
            return redirect('/welcome');
        }

        $categories = Category::all();
        $book_infos = Book_infos::all();
        $books = Book::whereIn('id', $book_ids = $book_infos->where('user_id', session('user_id'))->where('save', 1)->pluck('book_id'))->get();
        $college = College::all();
        $department = Department::all();

        $types = ['PDF ', 'Microsoft Word', 'Microsoft Excel', 'PowerPoint', 'Text Files', 'Programming Files', 'Executable Files', 'Database Files'];

        return view('library.my-book', [
            'categories' => $categories, 
            'book_infos' => $book_infos, 
            'books' => $books->where('is_active', 1),  
            'types' => $types,
            'colleges' => $college,
            'departments' => $department,
            'users' => User::all(),
            'fileIcons' => $this->fileIcons
        ]);
    }

    public function AddBook()
    {
        $categories = Category::all();
        $book_infos = Book_infos::all();
        $books = Book::all();
        $college = College::all();
        $department = Department::all();
        $types = ['عام', 'خاص', 'مشترك','محدد'];
        $levels = ['المستوى الاول','المستوى الثاني','المستوى الثالث','المستوى الرابع','المستوى الخامس','المستوى السادس','المستوى السابع'];
        $file_types = ['PDF ', 'Microsoft Word', 'Microsoft Excel', 'PowerPoint', 'Text Files', 'Programming Files', 'Executable Files', 'Database Files'];
        return view('library.add-book', [
            'categories' => $categories, 
            'book_infos' => $book_infos, 
            'books' => $books->where('is_active', 1),  
            'types' => $types, 
            'file_types' => $file_types,
            'colleges' => $college,
            'departments' => $department,
            'levels' => $levels
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
        $book = new Book();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'file_url' => 'required',
            'description' =>  'nullable|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // إذا وصلت إلى هنا، فهذا يعني أن البيانات صالحة
        $validatedData = $validator->validated();

        if ($request->hasFile('file_url')) {
            $file = $request->file('file_url');
            $code = rand(1000000000000, 9999999999999) ;
            $imgName = $code . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('', $imgName, 'library');
            $fileName = 'uploads/library/' . $path;
            if ($this->getFileType($file->getClientOriginalExtension()) == 'Unknown') {
                return redirect()->back()
                ->with('error', 'المكتبة لا تدعم هذا النوع من الملفات')
                ->withInput();
            }
            $book->code = $code;
            $book->title = $request->title;
            $book->author = $request->author;
            $book->category_id = $request->category_id; 
            $book->file_url = $fileName;
            $book->type = $request->type;
            $book->added_by = session('user_id');
            $book->description = $request->description;
            $book->file_type = $this->getFileType($file->getClientOriginalExtension());
            $book->file_size = $file->getSize();
            if ($request->type == 'خاص') {
                $book->college_id = $request->college_id;
                $book->department_id = $request->department_id;
            } 
            if ($request->type == 'مشترك') {
                $book->college_id = $request->college_id;
                $book->level = $request->level;
            } 
            if ($request->type == 'محدد') {
                $book->college_id = $request->college_id;
                $book->department_id = $request->department_id;
                $book->level = $request->level;
            }
            $book->save();
            return redirect('library');
        }

        return '404';
        //  $request;
    }

    function getFileType($extension) {
        // تحويل الامتداد إلى حروف صغيرة لتجنب المشاكل مع الحالة (Case Sensitivity)
        $extension = strtolower($extension);
    
        // مصفوفة تحوي أنواع الملفات والامتدادات الخاصة بها
        $fileTypes = [
            'PDF' => ['pdf'],
            'Microsoft Word' => ['doc', 'docx'],
            'Microsoft Excel' => ['xls', 'xlsx'],
            'PowerPoint' => ['ppt', 'pptx'],
            'Text Files' => ['txt'],
            'Programming Files' => ['html', 'json', 'xml'],
            'Executable Files' => ['exe', 'msi', 'dmg'],
            'Database Files' => ['mdb', 'accdb', 'sql'],
        ];
    
        // البحث عن الامتداد في المصفوفة
        foreach ($fileTypes as $type => $extensions) {
            if (in_array($extension, $extensions)) {
                return $type; // إرجاع نوع الملف
            }
        }
    
        return "Unknown"; // إذا لم يتم العثور على الامتداد
    }

    public function SaveBookInfo(Request $request)
    {
        $book_id = $request->input('book_id');
        $userId = session('user_id'); // الحصول على ID المستخدم الحالي

        
        $book = Book::find($book_id);
        // التحقق من عدم تكرار التفاعل
        $existingBookInfo = Book_infos::where('user_id', $userId)
            ->where('book_id', $book_id)
            ->first();

        if ($existingBookInfo) {
            $BookInfo = Book_infos::find($existingBookInfo->id);
            if ($existingBookInfo->save != 1) {
                $BookInfo->increment('save');
                $book->increment('save_count');
            } else {
                $BookInfo->decrement('save');
                $book->decrement('save_count');
            }
        } else {
            $BookInfo = new Book_infos();
            $BookInfo->book_id = $book_id;
            $BookInfo->user_id = $userId;
            $BookInfo->save = 1;
            $BookInfo->save();
            $book->increment('save_count');
        }

        return response()->json([
            'success' => true,
            'message' => $BookInfo,
        ]);
    }
    public function DownloadBookInfo(Request $request)
    {
        $book_id = $request->input('book_id');
        $userId = session('user_id'); // الحصول على ID المستخدم الحالي

        // التحقق من عدم تكرار التفاعل
        $existingBookInfo = Book_infos::where('user_id', $userId)
            ->where('book_id', $book_id)
            ->first();
            
        $book = Book::find($book_id);
        if ($existingBookInfo) {
            $BookInfo = Book_infos::find($existingBookInfo->id);
            $BookInfo->increment('downloads');
            $book->increment('download_count');
        } else {
            $BookInfo = new Book_infos();
            $BookInfo->book_id = $book_id;
            $BookInfo->user_id = $userId;
            $BookInfo->downloads = 1;
            $BookInfo->save();
            $book->increment('download_count');
        }

        return response()->json([
            'success' => true,
            'message' => $BookInfo,
        ]);
    }

    public function OpenBookInfo(Request $request)
    {
        $book_id = $request->input('book_id');
        $userId = session('user_id'); // الحصول على ID المستخدم الحالي

        // التحقق من عدم تكرار التفاعل
        $existingBookInfo = Book_infos::where('user_id', $userId)
            ->where('book_id', $book_id)
            ->first();
            
        $book = Book::find($book_id);
        if ($existingBookInfo) {
            $BookInfo = Book_infos::find($existingBookInfo->id);
            $BookInfo->increment('opens_count');
            $book->increment('opens_count');
        } else {
            $BookInfo = new Book_infos();
            $BookInfo->book_id = $book_id;
            $BookInfo->user_id = $userId;
            $BookInfo->opens_count = 1;
            $BookInfo->save();
            $book->increment('opens_count');
        }

        return response()->json([
            'success' => true,
            'message' => $BookInfo,
        ]);
    }
    public function LikeBookInfo(Request $request)
    {
        $book_id = $request->input('book_id');
        $userId = session('user_id'); // الحصول على ID المستخدم الحالي

        // التحقق من عدم تكرار التفاعل
        $existingBookInfo = Book_infos::where('user_id', $userId)
            ->where('book_id', $book_id)
            ->first();
            
        $book = Book::find($book_id);
        if ($existingBookInfo) {
            $BookInfo = Book_infos::find($existingBookInfo->id);
            if ($existingBookInfo->likes != 1) {
                $BookInfo->increment('likes');
                $book->increment('likes_count');

            } else {
                $BookInfo->decrement('likes');
                $book->decrement('likes_count');
            }
        } else {
            $BookInfo = new Book_infos();
            $BookInfo->book_id = $book_id;
            $BookInfo->user_id = $userId;
            $BookInfo->likes = 1;
            $BookInfo->save();
            $book->increment('likes_count');
        }

        return response()->json([
            'success' => true,
            'message' => $BookInfo,
        ]);
    }  

    public function GetBookInfo(Request $request)
    {
        $BookInfo = Book::all();
        $MyBookInfo = Book_infos::where('user_id', session('user_id'))->get();
        $data = [
            'book' => $BookInfo,
            'myBookInfo' => $MyBookInfo
        ];
        return response()->json($data);
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
