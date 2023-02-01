<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\UserController\delete;


class UserController extends Controller
{
    
    public function index()
    {
        return view('users.users', [
            'header'    => 'Users Management',
            'users'     => User::all()
        ]);
    }

    public function form()
    {
        return view('users.form', [
            'header'    => 'Add User',
        ]);
    }

    public function store(Request $request)
    {
        // For Validation
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        // For Storing after Validation
        User::create([
            'name'  =>  $request->name,
            'email'  =>  $request->email,
            'password'  =>  Hash::make($request->password),
        ]);

        session()->flash('status', 'Added User Successfully!');

        $request->validate([
            'image' => 'required|mimes:jpeg,png,bmp,gif|max: 2000'
            ]);
            $uploadImage = $request->file('image');
            $imageNameWithExt = $uploadImage->getClientOriginalName(); 
            $imageName =pathinfo($imageNameWithExt, PATHINFO_FILENAME);
            $imageExt=$uploadImage->getClientOriginalExtension();
            $storeImage=$imageName . time() . "." . $imageExt;
            $request->image->move(public_path('images'), $storeImage);
            $carousel= slider::create([
                'image' => $storeImage
            ]);

        // Redirect to the List of Users
        return redirect('/users');
    }

   
    public function show($id)
    {
        $user = User::find($id);

        return view('users.form', [
                'header'    => 'Update User',
                'users'      => $user
            ]); 
    }

    public function update(Request $request, $id)
    {
        // For Validation
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255']
        ]);

        $user = User::find($id);

        $user->update($request->all());

        session()->flash('status', 'Updated User Successfully!');

        return redirect('/users/update/' . $user->id);
    }

    
    public function delete($id)
    {

        $user = User::find($id);
        $user -> delete();

        $user->delete($id);

        return Redirect('/users');
    }

}
