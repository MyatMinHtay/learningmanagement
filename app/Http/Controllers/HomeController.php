<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Models\Course;

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

        $courses = Course::all();

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
