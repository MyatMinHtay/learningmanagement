<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;

class GlobalSearchController extends Controller
{
    public function globalSearch(Request $request)
    {
        $query = $request->input('q');

        $courses = Course::search($query)->get();



        return view('search', [
            'courses' => $courses,
            'query' => $query
        ]);
    }
}
