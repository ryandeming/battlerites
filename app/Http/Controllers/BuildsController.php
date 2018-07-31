<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Build;
use App\Hero;
use App\Ability;
use App\Battlerite;
use Auth;

class BuildsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            $pageTitle = "all builds";
            $builds = Build::orderBy('score', 'desc')->paginate(10);
            return view('builds.index')->with(['builds' => $builds, 'title' => $pageTitle]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($hero = null)
    {
        if($hero != null) {
            $hero = Hero::where('name', $hero)->firstOrFail();
            return view('builds.create')->with(['hero' => $hero, 'heroes' => null, 'battlerites' => $hero->battlerites]);
        } else {
        $heroes = Hero::all();
        return view('builds.create')->with('heroes', $heroes);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:12',
            'body' => 'required',
            'hero_id' => 'required',
            'build' => 'required|min:5'
        ]);
        if(count($request->input('build') > 1)) {
            $battlerites = '';
            foreach($request->input('build') as $battlerite) {
                $battlerites .= $battlerite.', ';
            }
            $battlerites = substr($battlerites, 0, -2);
        }
        else {
            $battlerites = $request->input('build');
        }
        // Check user
        if(Auth::guest()) {
            $user_id = 0;
        } else
        {
            $user_id = auth()->user()->id;
        }

        // Create build
        $build = new Build;
        $hero = Hero::where('id', $request->input('hero_id'))->firstOrFail();
        $build->slug = $hero->name.'-';
        $build->slug .= str_slug($request->input('title'), '-');
        $build->user_id = $user_id;
        $build->hero_id = $request->input('hero_id');
        $build->title = $request->input('title');
        $build->body = $request->input('body');
        $build->build = $battlerites;
        $build->save();

        return redirect('/builds')->with('success', 'Build Created');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $hero
     * @return \Illuminate\Http\Response
     */
    public function show($hero)
    {
        $pageTitle = $hero.' builds';
        $hero = Hero::where('name', $hero)->firstOrFail();
        $builds = Build::where('hero_id', $hero->id)->orderBy('featured', 'DESC')->orderBy('score', 'DESC')->orderBy('views', 'ASC')->paginate(10);
        return view('builds.index')->with(['title' => $pageTitle, 'builds' => $builds, 'hero' => $hero]);    
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pageTitle = 'who cares';
        $build = Build::find($id);
        $hero_id = $build->hero_id;
        $hero = Hero::find($hero_id);

        return view('builds.edit')->with(['build' => $build, 'hero' => $hero, 'title' => $pageTitle]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'hero_id' => 'required',
            'build' => 'required'
        ]);
        if(count($request->input('build') > 1)) {
            $battlerites = '';
            foreach($request->input('build') as $battlerite) {
                $battlerites .= $battlerite.', ';
            }
            $battlerites = substr($battlerites, 0, -2);
        }
        else {
            $battlerites = $request->input('build');
        }

        // Create build
        $build = Build::find($id);
        $build->slug = str_slug($request->input('title'), '-');
        $build->title = $request->input('title');
        $build->body = $request->input('body');
        $build->build = $battlerites;
        $build->save();

        return redirect('/builds')->with('success', 'Build Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function view($id)
    {
        $build = Build::find($id);
        $hero_id = $build->hero_id;
        $hero = Hero::find($hero_id);
        $pageTitle = $build->title;
        $battlerites = $hero->battlerites;
        $build->increment('views'); // or 'page_views'`
        return view('builds.show')->with(['build' => $build, 'hero' => $hero, 'title' => $pageTitle]);
    }
}
