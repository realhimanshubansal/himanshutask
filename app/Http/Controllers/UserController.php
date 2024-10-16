<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        $roles = Role::all();
        return view("users.create",compact('roles','users'));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'description' => 'required|string',
            'role_id' => 'required|exists:roles,id',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'name.required' => 'Name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already registered.',
            'phone.required' => 'Phone number is required.',
            'phone.regex' => 'Please enter a valid 10-digit Indian phone number.',
            'role_id.required' => 'Please select a valid role.',
            'profile_image.image' => 'Profile image must be an image file (jpeg, png, jpg).',
            'profile_image.max' => 'Profile image size should not exceed 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

         $profileImage = null;
         if ($request->hasFile('profile_image')) {
             $file = $request->file('profile_image');
             $profileImage = time() . '_' . $file->getClientOriginalName();
             $file->move(public_path('uploads'), $profileImage);
         }
 
         $user = User::create([
             'name' => $request->name,
             'email' => $request->email,
             'phone' => $request->phone,
             'description' => $request->description,
             'role_id' => $request->role_id,
             'profile_image' => $profileImage,
         ]);
         
         $userData = User::with('role')->where("id",$user->id)->first();
 
         return response()->json(['user' => $userData], 201);
    }


}
