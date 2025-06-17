<?php

namespace App\Http\Controllers;

// use App\Models\File;

use Exception;
use App\Models\Mtc;
use App\Models\User;
use App\Models\Courses;
use App\Models\CourseList;
use App\Models\SystemRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\QueryException;



class AuthController extends Controller
{
    public function index(Request $request)
    {

    

        if (Auth::check()) {
            $user = auth()->user();
        }else{
            $user = null;
        }

        return view('home');
    }

    public function create()
    {
        $systemroles = SystemRole::all();
        return view('auth.register',[
            'systemroles' => $systemroles
        ]);
    }

    //create
    public function store()
    {

        // dd(request()->all());
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
            'userphoto' => ['required,image,mimes:jpeg,png,jpg,svg,max:2048'],

        ], [
            'email.required' => 'we need your email address',
            'password.min' => 'Password should be more than 8 characters',

            'username.required' => 'username must be required',
            'password.password_confirmation' => "confirm password doesn't match with password"
        ]);


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


        $user = User::create($formData);


        //login 
        auth()->login($user, $remember = true);

        return redirect('/')->with('success', 'Welcome Dear, ' . $user->username);
    }

    public function login()
    {
        return view('auth/login');
    }

    //login
    public function postLogin(Request $request)
    {
       
        $formData = $request->validate([
            'email' => ['required', 'email', 'max:255', Rule::exists('users', 'email')],
            'password' => ['required', 'min:8', 'max:255']
        ], [
            'email.required' => 'We need your email address',
            'password.min' => 'Password should be more than 8 characters'
        ]);

        $remember = $request->has('remember');

        if (auth()->attempt($formData, $remember)) {

            $request->session()->regenerate();
            $username = auth()->user()->username;
            return redirect('/')->with('success', "Welcome Back $username");
        }

        // Check if the email is incorrect
        $user = User::where('email', $formData['email'])->first();
        if (!$user) {
            return back()->withErrors([
                'email' => 'The provided email address is incorrect.',
            ]);
        }

        // If the email is correct, the password must be incorrect
        return back()->withErrors([
            'password' => 'The provided password is incorrect.',
        ]);
    }

    public function updateprofile(Request $request)
    {


        $user = auth()->user();
        $id = $user->id;
        $imagepath = $user->userphoto;

        $formData = request()->validate([



            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'username' => 'nullable|regex:/^[A-Za-z0-9\-_]+$/|unique:users,username,' . $user->id,
            'usermtc_id' => 'required',
            'userphoto' => ['mimes:jpeg,png,jpg', 'max:2048', 'sometimes'],
            'password' => 'sometimes',
            'new_password' => 'nullable',
            'confirm_password' => 'sometimes|same:new_password',


        ], [
            'username.regex' => 'username must be character and number and (-) and (_) not allow other character and space'
        ]);






        if (isset($formData['password'])) {
            if (Hash::check($formData['password'], $user->password)) {
                $formData['password'] = Hash::make($formData['new_password']);

                unset($formData['new_password'], $formData['confirm_password']);
            } else {


                return redirect()->back()->with('error', 'Current password is incorrect.');
            }
        } else {
            unset($formData['password'], $formData['new_password'], $formData['confirm_password']);
        }


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

        try {


            $user = User::where('id', $id)->update($formData);
        } catch (QueryException $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }



        return redirect('/profile' . '/' . $formData['username'])->with('success', 'Update Success');
        //return back()->with('success','Update Success');
    }

    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Good bye');
    }

}
