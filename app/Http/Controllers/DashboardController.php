<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
        $courses = Course::all();
        $categories = Category::all();
        return view('dashboard', compact('courses', 'categories'));
    }
}
