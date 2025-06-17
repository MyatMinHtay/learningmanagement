<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Chapter;
use App\Models\Mtc;
use App\Models\Order;
use App\Models\SaveWebtoon;
use App\Models\StarPackage;
use App\Models\Subscribe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function showabout(){
        return view('about');
    }
    
    public function showTeam(){
        return view('team');
    }

    public function showTestimonial(){
        return view('testimonial');
    }

    public function showcourses(){
        return view('courses');
    }


    public function showprofile(User $user){

          
    
          
            
            return view('auth.profile',[
                
                'user' => $user,
                
            ]);
    }

    public function showeditprofile(){
        return view('auth.editprofile');
    }

   
}
