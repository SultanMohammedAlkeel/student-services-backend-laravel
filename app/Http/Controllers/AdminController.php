<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!session('logged_in')) {
            return redirect('/welcome');
        }

        $admins = Admin::all();
        $roles = DB::table('roles')->get();
        
        
        return view('admins.setup.admin', ['admins.' => $admins, 'roles' => $roles]);
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
        $admin = new Admin();
        $role = new Role();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'image_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $id = '';
        $imageName = '';


        if ($role->count() == 0) {
            $role->name = 'المشرف العام'; 
            $role->type = 'مشرف';
            $role->description = 'صلاحيات الاشراف و الادارة على كل شيء';
            $role->save();

            if (isset($admin->all()->last()->id)) {
                $id = $admin->all()->last()->id + 1;
            } else {
                $id = 1;
            }


            $image = $request->file('image_url');
            $imgName = $id . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('', $imgName, 'admins.');
            $imageName = 'images/profiles/admins/' . $path;

            $admin->name = $request->name;
            $admin->email = $request->email;
            $admin->password = Hash::make($request->password);
            $admin->image_url = $imageName;
            $admin->phone_number = $request->phone_number;
            $admin->role_id = 1;
            $admin->last_login = now();
            $admin->save();

            return redirect('/setup');
        }

        
        if (isset($admin->all()->last()->id)) {
            $id = $admin->all()->last()->id + 1;
        } else {
            $id = 1;
        }


        $image = $request->file('image_url');
        $imgName = $id . '.' . $image->getClientOriginalExtension();
        $path = $image->storeAs('', $imgName, 'admins.');
        $imageName = 'images/profiles/admins/' . $path;

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);
        $admin->image_url = $imageName;
        $admin->phone_number = $request->phone_number;
        $admin->role_id = 1;
        $admin->last_login = now();

        $admin->save();

        return redirect('/admin');
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
