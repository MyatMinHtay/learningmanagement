<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;

class GlobalSearchController extends Controller
{
    public function globalSearch(Request $request)
    {
        try {
            $query = $request->input('q');

            $courses = Course::search($query)->get();

            return view('search', [
                'courses' => $courses,
                'query' => $query
            ]);

        } catch (Exception $e) {
            Log::error('Error in globalSearch: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to perform search. Please try again.');
        }
    }
}
