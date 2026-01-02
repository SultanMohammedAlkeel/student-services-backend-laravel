<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Interaction;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
    */
    public function index()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }


        $posts = Post::all()->sortByDesc('id');


        return view('events.posts', ['posts' => $posts]);
        
    }

    function PostsManagement()  
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }

        $posts = Post::all();
        $users = User::all();
        $sendersWithCount = Post::select('sender_id as id', DB::raw('COUNT(*) as count'))
                    ->groupBy('sender_id')
                    ->get();
        return view('admins.management.posts', ['posts' => $posts, 'users' => $users, 'sendersWithCount' => $sendersWithCount]);
        
    }

    public function getPosts()
    {
        $userId = session('user_id'); // افترضنا أنك تستخدم نظام مصادقة Laravel وتحصل على id المستخدم الحالي

        $posts = DB::table('posts')
        ->join('users', 'posts.sender_id', '=', 'users.id')
        ->leftJoin('interactions', function ($join) use ($userId) {
            $join->on('posts.id', '=', 'interactions.post_id')
                ->where('interactions.user_id', '=', $userId)
                ->where(function ($query) {
                    $query->where('interactions.like', '=', 1)
                        ->orWhere('interactions.save', '=', 1);
                });
        })
        ->select(
            'posts.*',
            'users.name as user_name',
            'users.code as code',
            'users.image_url as user_image',
            DB::raw('CASE WHEN interactions.like = 1 THEN true ELSE false END as is_liked'),
            DB::raw('CASE WHEN interactions.save = 1 THEN true ELSE false END as is_saved')
        )
        ->orderBy('posts.id')
        ->where('posts.deleted', '=', 0)
        ->get();

        return response()->json($posts);
    }

    function getComments() {
        $comments = DB::table('comments')
        ->join('users', 'comments.user_id', '=', 'users.id')
        ->select('comments.*', 'users.name as user_name', 'users.image_url as user_image')
        ->orderByDesc('comments.id')
        ->get();

        return response()->json($comments);
    }

    // دالة لتسجيل التفاعل
    public function recordInteraction(Request $request)
    {
        $postId = $request->input('post_id');
        $userId = session('user_id'); // الحصول على ID المستخدم الحالي

        // التحقق من عدم تكرار التفاعل
        $existingInteraction = Interaction::where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();

        if (!$existingInteraction) {
            $interaction = new Interaction();
            $interaction->user_id = $userId;
            $interaction->post_id = $postId;
            $interaction->save();

            // زيادة عدد المشاهدات في جدول المنشورات (اختياري)
            $post = Post::find($postId);
            $post->increment('views_count');
        }

        return response()->json([
            'success' => true,
            'message' => 'Interaction recorded successfully.',
        ]);
    }  
    public function GetInteraction(Request $request)
    {
        $post = Post::all();
        $interaction = Interaction::where('user_id', session('user_id'))->get();
        $data = [
            'post' => $post,
            'interaction' => $interaction
        ];
        return response()->json($data);
    }    
    public function likeInteraction(Request $request)
    {
        $postId = $request->input('post_id');
        $userId = session('user_id'); // الحصول على ID المستخدم الحالي

        // التحقق من عدم تكرار التفاعل
        $existingInteraction = Interaction::where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();
            $post = Post::find($postId);
            $interaction = Interaction::find($existingInteraction->id); // استخدام find() للحصول على التفاعل();

        if ($existingInteraction->like == 0) {
            $interaction->increment('like');

            $post->increment('likes_count');
        } else {
            $interaction->decrement('like');
            $post->decrement('likes_count');
        }

        return response()->json([
            'success' => true,
            'message' => $interaction,
        ]);
    }  
    public function saveInteraction(Request $request)
    {
        $postId = $request->input('post_id');
        $userId = session('user_id'); // الحصول على ID المستخدم الحالي

        // التحقق من عدم تكرار التفاعل
        $existingInteraction = Interaction::where('user_id', $userId)
            ->where('post_id', $postId)
            ->first();
            $interaction = Interaction::find($existingInteraction->id); // استخدام find() للحصول على التفاعل();

        if ($existingInteraction->save == 0) {
            $interaction->increment('save');
        } else {
            $interaction->decrement('save');
        }

        return response()->json([
            'success' => true,
            'message' => $interaction,
        ]);
    }
    public function deletePost(Request $request)
    {
        $postId = $request->post_id;
        
        $post = Post::find($postId);
        $post->increment('deleted');

        return redirect()->back();
    }
    public function commentInteraction(Request $request)
    {
        $postId = $request->input('post_id');
        $userId = session('user_id'); 

        
        // التحقق من عدم تكرار التفاعل
        $comment = new Comment();
        $comment->user_id = $userId;
        $comment->post_id = $postId;
        $comment->content = $request->input('comment');
        $comment->save();

        $post = Post::find($postId);
        $post->increment('comments_count');

        return response()->json([
            'success' => true,
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
        $posts = new Post();
        $imageName = '';

        
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        
        if ($request->hasFile('file')) {
            $image = $request->file('file');
            $imgName = rand(100000000, 999999999) . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('', $imgName, 'post');
            $imageName = 'uploads/posts/' . $path;
            $posts->file_url = $imageName;
            
            $originalName = $image->getClientOriginalName();
            $fileNameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);
            $posts->file_name = $fileNameWithoutExtension;
            $posts->file_type = $this->getFileType($imageName);
            $posts->file_size = $image->getSize();
        }
        $posts->content = $request->content;
        $posts->sender_id = session('user_id');
        $posts->save();
        return redirect()->back();
    }

    function getFileType($filename) {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'flv', 'wmv'];
        $audioExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'flac'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'html', 'css', 'js', 'json', 'php', 'apk', 'exe', 'csv', 'xml'];
    
        if (in_array($extension, $imageExtensions)) {
            return 'صورة';
        } elseif (in_array($extension, $videoExtensions)) {
            return 'فيديو';
        } elseif (in_array($extension, $audioExtensions)) {
            return 'صوت';
        } elseif (in_array($extension, $documentExtensions)) {
            return 'ملف';
        } else {
            return 'نص';
        }
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
