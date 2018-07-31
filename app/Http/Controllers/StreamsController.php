<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Stream;

class StreamsController extends Controller
{
    public function store() {
        $streams = $this->getStreams();
        foreach($streams as $stream) {
            $thestream = Stream::where('user_id', $stream['user_id'])->first();
            if($thestream == null) {
                $thestream = new Stream;
            }
            $thestream->user_id = $stream['user_id'];
            $thestream->username = $stream['username'];
            $thestream->title = $stream['title'];
            $thestream->url = $stream['url'];
            $thestream->viewers = $stream['viewers'];
            
            $thestream->save();
        }
    }

    function getStreams() {

        $json_url = "https://api.twitch.tv/helix/streams?first=5&game_id=493277";
        $ch      = curl_init( $json_url );
        $options = array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => array( "Client-ID: wjyg2974c4uap5qwsu6pxa4kffgv5w" ),
        );
        curl_setopt_array( $ch, $options );
    
        $result = curl_exec( $ch );
        $result = json_decode($result, true);
        //return var_dump($result);
        if($result) {
            $i = 0;
            for($i = 0; $i < 5; $i++) {
                $stream[$i]['title'] = $result['data'][$i]['title'];
                $stream[$i]['viewers'] = $result['data'][$i]['viewer_count'];
                $stream[$i]['user_id'] = $result['data'][$i]['user_id'];
                $stream[$i]['username'] = $this->getUsername($result['data'][$i]['user_id']);
                $stream[$i]['url'] = "http://twitch.tv/".$stream[$i]['username'];
            }
            return $stream;
        } else {
            return null;
        }
    } 

    function getUsername($userId) {
            $json_url = "https://api.twitch.tv/helix/users?id=".$userId;
            $ch      = curl_init( $json_url );
            $options = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => array( "Client-ID: wjyg2974c4uap5qwsu6pxa4kffgv5w" ),
            );
            curl_setopt_array( $ch, $options );
        
            $result = curl_exec( $ch );
            $result = json_decode($result, true);
            //return var_dump($result);
            if($result) {
                return $result['data'][0]['display_name'];
            } else {
                return null;
            }
            //display_name
    }
}
