<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Contact;
use App\Models\Department;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!session('logged_in')) { 
            return redirect('/welcome');
        }
        $colleges = College::all();
        $departments = Department::all();
        $roles = Role::all();
        
        $friend_id = User::all()->where('id', '!=', session('user_id'))->whereNotIn('id', DB::table('contacts')->where('user_id', session('user_id'))->pluck('friend_id'));
        $user_id = User::all()->where('id', '!=', session('user_id'))->whereNotIn('id', DB::table('contacts')->where('friend_id', session('user_id'))->pluck('user_id'));

        $users = $friend_id->intersect($user_id);
        $levels = ['المستوى الاول','المستوى الثاني','المستوى الثالث','المستوى الرابع','المستوى الخامس','المستوى السادس','المستوى السابع'];
        $types = ['مشرف','معلم','طالب'];    
        return view('chats.contacts', ['departments' => $departments, 'colleges' => $colleges, 'levels' => $levels, 'types' => $types, 'users' => $users, 'roles' => $roles]);
    }

    public function BlockUser(Request $request)
    {
        $contact = Contact::find($request->contact_id);
        if ($contact) {
            if ($contact->user_id == session('user_id')) {
                $contact->user_blocked = $contact->user_blocked ? 0 : 1;
            } else {
                $contact->friend_blocked = $contact->friend_blocked ? 0 : 1;
            }
            $contact->save();
        }
        return redirect()->back();
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
        $contact = new Contact();
        $contact->friend_id = $request->friend_id;
        $contact->user_id = session('user_id');
        $contact->friend_type = $request->friend_type;
        $contact->save();
        
        return redirect('chat');
    }

    
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');
        $users =   DB::table('users')
        ->join('roles', 'users.role_id', '=', 'roles.id')
        ->select('users.id', 'users.name', 'roles.name as role_name', 'users.image_url', 'users.user')
        ->where('users.name', 'like', '%' . $keyword . '%')
        ->get();
        return response()->json($users);
    }

    public function SelectUserType(Request $request) 
    {
        $colleges = College::all();
        $departments = Department::all();
        $roles = Role::all();
        
        $friend_id = User::all()->where('id', '!=', session('user_id'))->whereNotIn('id', DB::table('contacts')->where('user_id', session('user_id'))->pluck('friend_id'));
        $user_id = User::all()->where('id', '!=', session('user_id'))->whereNotIn('id', DB::table('contacts')->where('friend_id', session('user_id'))->pluck('user_id'));

        $users = $friend_id->intersect($user_id);
        $users = $users->where('user', $request->type);
        
        if ($request->type == 'معلم') {
            $ids = Teacher::where('college_id', $request->college_id)
            ->where('department_id', $request->department_id)
            ->pluck('id')
            ->toArray();
            $users = $users->whereIn('user_id', $ids);
        }
        if ($request->type == 'طالب') {
            $ids = Student::where('department_id', $request->department_id)
            ->where('level', $request->level)
            ->pluck('id')
            ->toArray();
            $users = $users->whereIn('user_id', $ids);
        }
        $levels = ['المستوى الاول','المستوى الثاني','المستوى الثالث','المستوى الرابع','المستوى الخامس','المستوى السادس','المستوى السابع'];
        $types = ['مشرف','معلم','طالب'];    
        return view('chats.contacts', ['departments' => $departments, 'colleges' => $colleges, 'levels' => $levels, 'types' => $types, 'users' => $users, 'roles' => $roles]);
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
