<?php

namespace App\Http\Controllers;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\SystemRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{


    public function index(Request $request)
    {
        try {
            $name = $request->input('name');
            $role = $request->input('role');
            $email = $request->input('email');
            if ($name || $role || $email) {
                $query = User::query();

                if ($name) {
                    $query->where('username', 'LIKE', '%' . $name . '%');
                }

                if ($role) {
                    $query->where('role', $role);
                }

                if ($email) {
                    $query->where('email', 'LIKE', '%' . $email . '%');
                }

                $users = $query->join('system_roles', 'users.role_id', '=', 'system_roles.id')
                    ->select('users.*', 'system_roles.role', 'system_roles.id as roleid')->paginate(30);
            } else {
                $users =  User::latest()
                    ->join('system_roles', 'users.role_id', '=', 'system_roles.id')
                    ->select('users.*', 'system_roles.role', 'system_roles.id as roleid')
                    ->paginate(30)->withQueryString();
            }


            $systemroles = SystemRole::all();

            return view('admin.users.index', [
                "users" => $users,
                "systemroles" => $systemroles,
                "request" => $request,
            ]);
        } catch (Exception $e) {
            Log::error('Error in users index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load users. Please try again.');
        }
    }

    public function createuser()
    {

      

        $formData = request()->validate([

            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'username' => ['required', 'max:255', 'min:3', Rule::unique('users', 'username'), 'regex:/^[A-Za-z0-9]+$/'],
            'password' => [
                'required',
                'confirmed', // Make sure the password confirmation field is present and matches the password field
                'min:8', // Require a minimum of 8 characters
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/'
                // Require at least one lowercase letter, one uppercase letter, one number, and one special character from @$!%*?&
            ],
            'userphoto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
            'role_id' => ['required']

        ], [
            'email.required' => 'we need your email address',
            'password.min' => 'Password should be more than 8 characters',

            'username.required' => 'username must be required'

        ]);

        $formData['status'] = "A";


        if ($file = request()->file('userimg')) {

            $image_name = md5(rand(1000, 10000));
            $ext = strtolower($file->getClientOriginalExtension());
            $image_full_name = $image_name . '.' . $ext;
            $upload_path = './assets/avatars/';
            $image_url = $upload_path . $image_full_name;
            $file->move($upload_path, $image_full_name);
            $formData['userphoto'] = $image_url;
        } else {
            $formData['userphoto'] = "./assets/avatars/user.png";
        }


        try {
            $user = User::create($formData);

            return redirect()->route('users')->with('success', 'User ' . $user->username . '  Account Created Successfully ');
        } catch (QueryException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edituser(User $user, Request $request)
    {
        try {
            $systemroles = SystemRole::all();
            return view('admin.users.edit', [
                'user' => $user,
                'systemroles' => $systemroles,
                'request' => $request
            ]);
        } catch (Exception $e) {
            Log::error('Error in edituser: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load user edit form. Please try again.');
        }
    }

    public function updateuser(User $user, Request $request)
    {

        $imagepath = auth()->user()->userphoto;

      

        $formData = $request->validate([

            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'max:255', 'min:3', Rule::unique('users')->ignore($user->id), 'regex:/^[A-Za-z0-9]+$/'],
            'role_id' => ['required', 'integer'],
            'userphoto' => ['mimes:jpeg,png,jpg', 'max:2048', 'sometimes'],
            'password' => 'nullable|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/',
            'password_confirmation' => 'sometimes|same:password',
        ], [
            'email.required' => 'required email address',
            'username.required' => 'Username is required',
        ]);






        if ($file = request()->file('userphoto')) {


            if ($imagepath != './assets/avatars/user.png') {
                if (File::exists(public_path() . $imagepath)) {
                    File::delete(public_path() . $imagepath);
                }
            }

            $image_name = md5(rand(1000, 10000));
            $ext = strtolower($file->getClientOriginalExtension());
            $image_full_name = $image_name . '.' . $ext;
            $upload_path = './assets/avatars/';
            $image_url = $upload_path . $image_full_name;


            $file->move($upload_path, $image_full_name);

            $formData['userphoto'] = $image_url;
        }

        $formData['userupdatedby'] = auth()->user()->username;



        try {
            $user->update($formData);

            return redirect()->route('users')->with('success', 'User updated successfully');
        } catch (QueryException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    public function deleteuser(User $user)
    {
        try {
            $user->status = "D";
            $user->save();

            return redirect()->route('users')->with('success', 'User Account Delete Successfully');
        } catch (QueryException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function lockuser(User $user)
    {
        try {
            $user->status = "L";
            $user->save();

            return redirect()->route('users')->with('success', 'User Account Locked Successfully');
        } catch (QueryException $e) {
            Log::error('Error in lockuser: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to lock user account. Please try again.']);
        }
    }

    public function unlockuser(User $user)
    {
        try {
            $user->status = "A";
            $user->save();

            return redirect()->route('users')->with('success', 'User Account Unlocked Successfully');
        } catch (QueryException $e) {
            Log::error('Error in unlockuser: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to unlock user account. Please try again.']);
        }
    }

}
