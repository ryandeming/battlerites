<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Build;
use App\Rating;
use Auth;

class RatingsController extends Controller
{
    //
    public function rate($build_id, $rate) {
        if($build_id == null) {
            return redirect('/builds')->with('error', 'No Build Selected, how did you get here?');
        }
        if(Auth::guest()) {
            return redirect('/builds/view/'.$build_id)->with('error', 'You must be logged in to rate.');
        } else {
            $user_id = auth()->user()->id;
        }
        $rating = Rating::where(['build_id' => $build_id, 'user_id' => $user_id])->first();
        if($rating == null) {
            $rating = new Rating;
        }
            $rating->build_id = $build_id;
            $rating->user_id = $user_id;
            $rating->rated = $rate;
            $rating->save();
            
            $build = Build::where('id', $build_id)->first();
            if($build != null) {
                $up = count(Rating::where(['build_id' => $build_id, 'rated' => 'up']));
                $down = count(Rating::where(['build_id' => $build_id, 'rated' => 'down']));
                $score = $up - $down;
                if($rate == 'up') {
                    $score++;
                } else {
                    $score--;
                }
                $build->score = $score;
                $build->save();
            }
            return redirect('/builds/view/'.$build_id)->with('success', 'Build Rated');
    }
}
