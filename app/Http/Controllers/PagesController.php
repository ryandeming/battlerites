<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index() {
        return view('pages.index');
    }

    public function about() {
        return view('pages.about');
    }

    public function player($playerName = null) {
        
        return view('pages.player')->with(['player' => $playerName, 'title' => 'Player Stats Lookup']);
    }

    public function services() {
        $data = array(
            'title' => 'Services',
            'desc' => 'Who gives a shit',
            'services' => ['Web Design', 'Programming', 'SEO']
        );
        return view('pages.services')->with($data);
    }
}
