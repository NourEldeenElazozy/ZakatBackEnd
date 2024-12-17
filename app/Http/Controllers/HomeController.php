<?php

namespace App\Http\Controllers;

use App\Models\campaign;
use App\Models\categorie;
use App\Models\donation;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $countcampaign=campaign::count();
       $countcategorie=categorie::count();
       $countUser=User::count();
       $countdonation=donation::count();




       return view('home',compact('countcampaign','countcategorie','countUser','countdonation'));
    }
}
