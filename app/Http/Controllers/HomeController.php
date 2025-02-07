<?php

namespace App\Http\Controllers;

use Auth;

class HomeController extends Controller
{
    const RECENT_INCIDENTS_COUNT = 3;
    const RECENT_REQUESTS_COUNT = 3;

    public function index()
    {
        $user = Auth::user();
        $data = [];

        if($user){
            $incidents = $user->incidents()->with(['category', 'caller', 'resolver'])->latest()->take(self::RECENT_INCIDENTS_COUNT)->get();
            $requests = $user->requests()->with(['category', 'caller', 'resolver'])->latest()->take(self::RECENT_REQUESTS_COUNT)->get();
            if($incidents->count() > 0){
                $data['incidents'] = $incidents;
            }
            if($requests->count() > 0){
                $data['requests'] = $requests;
            }
        }

        return view('index', $data);
    }
}
