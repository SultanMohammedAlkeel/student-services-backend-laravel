<?php

namespace App\Http\Controllers;

use App\Models\Chat_messages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }

        $friend_id = User::all()->where('id', '!=', session('user_id'))->whereIn('id', DB::table('contacts')->where('user_id', session('user_id'))->pluck('friend_id'));
        $user_id = User::all()->where('id', '!=', session('user_id'))->whereIn('id', DB::table('contacts')->where('friend_id', session('user_id'))->pluck('user_id'));

        $users = $friend_id->merge($user_id);
        $contact = DB::table('contacts')->where('user_id', session('user_id'))->orWhere('friend_id', session('user_id'))->get();
        $contact_id = collect([]);

        $unread = DB::table('chat_messages')->where('receiver_id', session('user_id'))->where('is_read', 0)->get();
        $lastMessage = DB::table('chat_messages')->where('receiver_id', session('user_id'))->orWhere('sender_id', session('user_id'))->select('id', 'message', 'receiver_id', 'sender_id')->orderBy('id', 'asc')->get();
        $lmsg = collect([]);

        foreach ($lastMessage as $msg) {
            if ($msg->receiver_id == session('user_id')) {
                $lmsg->put($msg->sender_id, $msg->message);
            } else {
                $lmsg->put($msg->receiver_id, $msg->message);
            }
        }

        foreach ($contact as $con) {
            if ($con->user_id != session('user_id')) {
                $contact_id->put($con->user_id, $con->id);
            } else {
                $contact_id->put($con->friend_id, $con->id);
            }
        }
        return view('chats.chat', ['users' => $users, 'contact' => $contact_id, 'unread' => $unread, 'lastMessage' => $lastMessage, 'lmsg' => $lmsg]);
    }

    public function readChat(Request $request)
    {
        $chatId = $request->input('chat_id');
        $userId = session('user_id');

       
        $existingInteraction = Chat_messages::where('id', $chatId)->where('is_read', 1)->first();

        if (!$existingInteraction) {
            $chat = Chat_messages::find($chatId);
            $chat->is_read = 1;
            $chat->save();
        }

        return response()->json([
            'success' => $chatId,
            'message' => 'Interaction recorded successfully.',
        ]);
    }    
    public function getChat($id)
    {
        $userId = session('user_id'); 
        $chat = DB::table('chat_messages')->where('contact_id', $id)->where('is_deleted', '!=', 1)->orderBy('id')->get();
        return response()->json($chat);
    }
    
    public function getContact($id)
    {
        $myid = session('user_id');
        $contact = DB::table('contacts')->where('user_id', $id)->orWhere('friend_id', $id)->where('friend_id', $myid)->orWhere('user_id', $myid)->get();
        return response()->json($contact);
    }

    public function getUser($id)
    {
       
        $user = User::select('id', 'code', 'name', 'image_url', 'last_login', 'role_id', 'status', 'user', 'user_id')->find($id);

       
        $myid = session('user_id');
        $contact = DB::table('contacts')
        ->where(function ($query) use ($id, $myid) {
            $query->where('user_id', $id)
                  ->orWhere('user_id', $myid);
        })
        ->where(function ($query) use ($id, $myid) {
            $query->where('friend_id', $id)
                  ->orWhere('friend_id', $myid);
        })
        ->first();

       
        if ($user) {
           
            $data = [
                'user' => $user,
                'contact' => $contact,
            ];

           
            return response()->json($data);
        } else {
           
            return response()->json(['error' => 'User not found'], 404);
        }
    }

    public function getUserInof()
    {
        $friend_id = User::all()->where('id', '!=', session('user_id'))->whereIn('id', DB::table('contacts')->where('user_id', session('user_id'))->pluck('friend_id'));
        $user_id = User::all()->where('id', '!=', session('user_id'))->whereIn('id', DB::table('contacts')->where('friend_id', session('user_id'))->pluck('user_id'));

        $users = $friend_id->merge($user_id);
        $contact = DB::table('contacts')->where('user_id', session('user_id'))->orWhere('friend_id', session('user_id'))->get();
        $contact_id = collect([]);

        $unread = DB::table('chat_messages')->where('receiver_id', session('user_id'))->where('is_read', 0)->where('is_deleted', '=', 0)->get();
        $lastMessage = DB::table('chat_messages')->where('is_deleted', '=', 0)->where('receiver_id', session('user_id'))->orWhere('sender_id', session('user_id'))->select('id', 'message', 'receiver_id', 'sender_id')->orderBy('id', 'asc')->get();
        $lmsg = collect([]);

        $i = 0;
        $index = [];
        foreach ($lastMessage as $msg) {
            if ($msg->receiver_id == session('user_id')) {
                if (!array_key_exists($msg->sender_id, $index)) {
                    $index[$msg->sender_id] = $i;
                    $i++;
                }
                $lmsg->put($index[$msg->sender_id], [$msg->sender_id, $msg->message, $unread->where('sender_id', $msg->sender_id)->count()]);
            } else {
                if (!array_key_exists($msg->receiver_id, $index)) {
                    $index[$msg->receiver_id] = $i;
                    $i++;
                }
                $lmsg->put($index[$msg->receiver_id], [$msg->receiver_id, $msg->message, $unread->where('sender_id', $msg->receiver_id)->count()]);
            }
        }

        foreach ($contact as $con) {
            if ($con->user_id != session('user_id')) {
                $contact_id->put($con->user_id, $con->id);
            } else {
                $contact_id->put($con->friend_id, $con->id);
            }
        }

       
        if ($users) {
            return response()->json($lmsg);
        } else {
           
            return response()->json(['error' => 'User not found'], 404);
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $chat = new Chat_messages();
        $chat->contact_id = $request->contact_id;
        $chat->message = $request->message;
        $chat->sender_id = session('user_id');
        $chat->receiver_id = $request->receiver_id;
        $chat->has_media = $request->has_media;

        if ($request->has_media) {
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $imgName = rand(1000000000000, 9999999999999) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('', $imgName, 'chat');
                $fileName = 'uploads/chats/' . $path;
                $chat->file_url = $fileName;
                $originalName = $file->getClientOriginalName();
                $fileNameWithoutExtension = pathinfo($originalName, PATHINFO_FILENAME);
                $chat->file_name = $fileNameWithoutExtension;
                $chat->type = $this->getFileType($file->getClientOriginalName());
                $chat->size = $file->getSize();
            }
        }
        
        $chat->save();
        

        return response()->json([
            'success' => true,
        ]);
    }
    public function deleteChat($id)
    {
        $chat = Chat_messages::find($id);

        $chat->is_deleted = 1;
        $chat->save();

        return response()->json([
            'success' => true,
            'message' => 'Chat deleted successfully',
            'id' => $id
        ]);
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
       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       
    }
}
