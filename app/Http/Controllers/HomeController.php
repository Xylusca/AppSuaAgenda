<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function show(): View
    {
        $services = Service::select(['id', 'name', 'desc', 'price', 'duration', 'image_path'])->get();

        $title = 'Home';

        return view('pages.home', compact('services', 'title'));
    }
}
