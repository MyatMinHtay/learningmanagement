<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\SystemRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function show(){

     
    
        if(Auth::check()){

            $users = User::count();
            $systemroles = SystemRole::all();
            

          
                
            if(auth()->user()->role->role == 'administrator' || auth()->user()->role->role == "admin" || auth()->user()->role->role == "author"){
                return view('admin.dashboard.dashboard',[
                    'systemroles' => $systemroles
                ]);
            }else if(auth()->user()->role->role == 'user'){
                return redirect()->back()->with('warning','access deined! You Are Not Admin');
            }
        }else{
            return redirect('/login')->with('warning','access deined! Only Admin Can Access This Page');
        }
    }
}
