<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat_messages;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChatApiController extends Controller
{
   public function index()
{
    $userId = Auth::id();

    // Get all contacts where user is either user_id or friend_id
    $contacts = Contact::where('user_id', $userId)
        ->orWhere('friend_id', $userId)
        ->with(['user', 'friend'])
        ->get();

    // Get list of blocked users IDs
    $blockedUserIds = $contacts->filter(function ($contact) use ($userId) {
        // إذا كان المستخدم الحالي هو user_id والطرف الآخر محظور
        if ($contact->user_id == $userId && $contact->user_blocked == 1) {
            return true;
        }
        // إذا كان المستخدم الحالي هو friend_id والطرف الآخر محظور
        if ($contact->friend_id == $userId && $contact->friend_blocked == 1) {
            return true;
        }
        return false;
    })->map(function ($contact) use ($userId) {
        // إرجاع ID الطرف الآخر (غير المستخدم الحالي)
        return $contact->user_id == $userId ? $contact->friend_id : $contact->user_id;
    })->unique()->values()->toArray();

    $chats = $contacts->map(function ($contact) use ($userId) {
        $otherUser = $contact->user_id == $userId ? $contact->friend : $contact->user;

        $lastMessage = Chat_messages::where(function ($query) use ($contact) {
            $query->where('contact_id', $contact->id);
        })
            ->orderBy('created_at', 'desc')
            ->first();

        $unreadCount = Chat_messages::where('contact_id', $contact->id)
            ->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->count();

        return [
            'id' => $contact->id,
            'name' => $otherUser->name,
            'image_url' => $otherUser->image_url,
            'last_message' => $lastMessage ? $lastMessage->message : null,
            'last_message_time' => $lastMessage ? $lastMessage->created_at->toDateTimeString() : null,
            'unread_count' => $unreadCount,
            'is_online' => $otherUser->status == 'متصل',
            'is_blocked' => $contact->user_id == $userId ? $contact->user_blocked : $contact->friend_blocked,
            'type' => 'individual',
        ];
    });

    return response()->json([
        'users' => $chats,
        'blocked_users' => $blockedUserIds
    ]);
}

    public function messages($contactId)
    {
        $userId = Auth::id();

        Chat_messages::where('contact_id', $contactId)
            ->where('receiver_id', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        $messages = Chat_messages::where('contact_id', $contactId)
            ->where('is_deleted', 0)
            ->orderBy('created_at', 'asc') // أو 'desc' حسب احتياجك
            ->get()
            ->map(function ($message) use ($userId) {
                return [
                    'id' => $message->id,
                    'chat_id' => $message->contact_id,
                    'content' => $message->message,
                    'created_at' => $message->created_at->toDateTimeString(),
                    'is_mine' => $message->sender_id == $userId,
                    'is_read' => $message->is_read,
                    'file_url' => $message->file_url ? asset($message->file_url, false) : null,
                    'file_type' => $message->type,
                    'file_size' => $message->size,
                ];
            });

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $userId = Auth::id();
        $contactId = $request->input('contact_id');
        $messageText = $request->input('message');
        $hasMedia = $request->input('has_media', 0);

        $contact = Contact::find($contactId);
        if (!$contact) {
            return response()->json(['error' => 'Contact not found'], 404);
        }

        $receiverId = $contact->user_id == $userId ? $contact->friend_id : $contact->user_id;

        $message = new Chat_messages();
        $message->contact_id = $contactId;
        $message->message = $messageText;
        $message->sender_id = $userId;
        $message->receiver_id = $receiverId;
        $message->has_media = $hasMedia;

        if ($hasMedia && $request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('chat_files', $fileName, 'public');
            $message->file_url = ('storage/' . $path);
            $message->type = $this->getFileType($file->getClientOriginalExtension());
            $message->size = $file->getSize();
        }

        $message->save();

        return response()->json([
            'id' => $message->id,
            'chat_id' => (int)$message->contact_id,
            'content' => $message->message,
            'created_at' => $message->created_at->toDateTimeString(),
            'is_mine' => true,
            'is_read' => false,
            'file_url' => $message->file_url,
            'file_type' => $message->type,
            'file_size' => $message->size,
        ]);
    }

    public function downloadFile(Request $request)
    {
        $fileUrl = $request->input('file_url');

        // التحقق من وجود الرابط
        if (!$fileUrl) {
            return response()->json(['error' => 'File URL is required'], 400);
        }

        // استخراج المسار من الرابط
        $path = str_replace(asset('storage/'), '', $fileUrl);
        $fullPath = storage_path('app/public/' . $path);

        // التحقق من وجود الملف
        if (!file_exists($fullPath)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // إرجاع الملف كاستجابة للتحميل
        return response()->download($fullPath);
    }
    public function markAsRead($messageId)
    {
        $message = Chat_messages::find($messageId);
        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        $message->is_read = 1;
        $message->save();

        return response()->json(['success' => true]);
    }

    public function deleteMessage($messageId)
    {
        $message = Chat_messages::find($messageId);
        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        $message->is_deleted = 1;
        $message->save();

        return response()->json(['success' => true]);
    }

    private function getFileType($extension)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'flv', 'wmv'];
        $audioExtensions = ['mp3', 'wav', 'ogg', 'm4a', 'flac'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];

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



    public function getAvailableUsers(Request $request)
    {
        try {
            $userId = Auth::id();
            $type = $request->query('type');

            Log::info("جلب المستخدمين المتاحين للمستخدم: $userId. النوع: $type");

            // جلب المستخدمين الذين ليسوا في قائمة الاتصالات
            $existingContactIds = Contact::where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('friend_id', $userId);
            })->get()->flatMap(function ($contact) use ($userId) {
                return [$contact->user_id, $contact->friend_id];
            })->unique()->filter(function ($id) use ($userId) {
                return $id != $userId;
            });

            $query = User::where('id', '!=', $userId)
                ->whereNotIn('id', $existingContactIds);

            // تطبيق التصفية حسب النوع إذا تم تحديده
            if ($type && $type !== 'الكل') {
                $query->where('user', $type);
                Log::info("تطبيق تصفية النوع: $type");
            }

            $users = $query->get();

            $availableUsers = [];
            foreach ($users as $user) {
                $availableUsers[] = [
                    'id' => $user->id,
                    'name' => $user->name ?? '',
                    'email' => $user->email ?? '',
                    'user' => $user->user ?? '',
                    'image_url' => $user->image_url ? url($user->image_url) : '',
                    'level' => $user->level ?? '',
                    'isOnline' => $user->is_online ?? false,
                ];
            }

            Log::info("تم جلب " . count($availableUsers) . " مستخدم متاح");

            return response()->json([
                'success' => true,
                'data' => $availableUsers
            ]);
        } catch (\Exception $e) {
            Log::error("خطأ في جلب المستخدمين المتاحين: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب المستخدمين المتاحين'
            ], 500);
        }
    }

    public function addContact(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'friend_id' => 'required|integer|exists:users,id',
                'friend_type' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 400);
            }

            $userId = Auth::id();
            $friendId = $request->friend_id;
            $friendType = $request->friend_type;

            Log::info("إضافة جهة اتصال جديدة - المستخدم: $userId . الصديق: $friendId . النوع: $friendType");

            // التحقق من عدم وجود الاتصال مسبقاً
            $existingContact = Contact::where(function ($query) use ($userId, $friendId) {
                $query->where('user_id', $userId)->where('friend_id', $friendId);
            })->orWhere(function ($query) use ($userId, $friendId) {
                $query->where('user_id', $friendId)->where('friend_id', $userId);
            })->first();

            if ($existingContact) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذا المستخدم موجود بالفعل في قائمة الاتصالات'
                ], 400);
            }

            // إنشاء جهة اتصال جديدة (نفس منطق ContactController)
            $contact = new Contact();
            $contact->friend_id = $friendId;
            $contact->user_id = $userId;
            $contact->friend_type = $friendType;
            $contact->save();

            Log::info("تم إنشاء جهة اتصال جديدة بنجاح - ID: " . $contact->id);

            // جلب بيانات المستخدم المضاف
            $addedUser = User::find($friendId);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة المستخدم بنجاح',
                'data' => [
                    'contact_id' => $contact->id,
                    'user' => [
                        'id' => $addedUser->id,
                        'name' => $addedUser->name ?? '',
                        'email' => $addedUser->email ?? '',
                        'user_type' => $addedUser->user ?? '',
                        'image_url' => $addedUser->image_url ? url($addedUser->image_url) : '',
                        'level' => $addedUser->level ?? '',
                        'isOnline' => $addedUser->is_online ?? false,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error("خطأ في إضافة جهة الاتصال: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إضافة المستخدم'
            ], 500);
        }
    }

    public function createChat(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'participants' => 'required|array|min:1',
                'participants.*' => 'integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صحيحة',
                    'errors' => $validator->errors()
                ], 400);
            }

            $userId = Auth::id();
            $participants = $request->participants;

            Log::info("إنشاء محادثة جديدة للمستخدم: $userId .مع المشاركين: " . implode(', ', $participants));

            // منطق إنشاء المحادثة (يمكن تطويره لاحقاً)

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء المحادثة بنجاح'
            ]);
        } catch (\Exception $e) {
            Log::error("خطأ في إنشاء المحادثة: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إنشاء المحادثة'
            ], 500);
        }
    }

    public function blockUser(Request $request)
    {
        try {
            Log::info('بدء حظر/إلغاء حظر مستخدم', ['request_data' => $request->all()]);

            $validator = Validator::make($request->all(), [
                "contact_id" => "required|integer|exists:contacts,id"
            ]);

            if ($validator->fails()) {
                Log::error('فشل في التحقق من صحة البيانات', ['errors' => $validator->errors()]);
                return response()->json([
                    "error" => "بيانات غير صحيحة",
                    "details" => $validator->errors()
                ], 422);
            }

            $userId = Auth::id();
            $contact = Contact::find($request->contact_id);

            if (!$contact) {
                Log::error('جهة الاتصال غير موجودة: ' . $request->contact_id);
                return response()->json(["error" => "جهة الاتصال غير موجودة"], 404);
            }

            if ($contact->user_id == $userId) {
                // $contact->user_blocked = !$contact->user_blocked;
                $contact->user_blocked = $contact->user_blocked ? 0 : 1;

            } else {
                // $contact->friend_blocked = !$contact->friend_blocked;
                $contact->friend_blocked = $contact->friend_blocked ? 0 : 1;
            }

            $contact->save();

            Log::info('تم تحديث حالة الحظر بنجاح');
            return response()->json(["success" => true]);
        } catch (\Exception $e) {
            Log::error('Error in blockUser: ' . $e->getMessage());
            return response()->json(["error" => "فشل في حظر المستخدم", "details" => $e->getMessage()], 500);
        }
    }
    public function unblockUser(Request $request)
    {
        try {
            Log::info("بدء إلغاء حظر مستخدم", ["request_data" => $request->all()]);

            $validator = Validator::make($request->all(), [
                "contact_id" => "required|integer|exists:contacts,id"
            ]);

            if ($validator->fails()) {
                Log::error("فشل في التحقق من صحة البيانات لإلغاء الحظر", ["errors" => $validator->errors()]);
                return response()->json([
                    "error" => "بيانات غير صحيحة",
                    "details" => $validator->errors()
                ], 422);
            }

            $userId = Auth::id();
            $contact = Contact::find($request->contact_id);

            if (!$contact) {
                Log::error("جهة الاتصال غير موجودة لإلغاء الحظر: " . $request->contact_id);
                return response()->json(["error" => "جهة الاتصال غير موجودة"], 404);
            }

            if ($contact->user_id == $userId) {
                $contact->user_blocked = false;
            } else {
                $contact->friend_blocked = false;
            }

            $contact->save();

            Log::info("تم تحديث حالة إلغاء الحظر بنجاح");
            return response()->json(["success" => true]);
        } catch (\Exception $e) {
            Log::error("Error in unblockUser: " . $e->getMessage());
            return response()->json(["error" => "فشل في إلغاء حظر المستخدم", "details" => $e->getMessage()], 500);
        }
    }
}