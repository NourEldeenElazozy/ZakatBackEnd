<?php

namespace App\Http\Controllers;

use App\Models\donation;
use Illuminate\Support\Facades\Auth;
use App\Models\campaign;
use App\Models\categorie;
use App\Models\User;
use App\Models\user_donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DonationsController extends Controller
{

    public function index()
    {

        $campaign = campaign::latest()->get();
        $categorie = categorie::latest()->get();
        $donation = donation::all();
        $users = User::all();


        return view('donations', compact('campaign', 'categorie', 'donation', 'users'));
    }




    public function create()
    {
    }





    public function store(Request $request)
    {
        try {

            $donations = new donation();
            $donations->id =  $request->id;
            $donations->amount = $request->amount;
            $donations->date = $request->date;
            $campaign = Campaign::findOrFail($request->campaign_id);
            $donationAmount = $request->amount;


            if ($donationAmount > 0) {

                $campaign->total -= $donationAmount;
                $campaign->paid_up += $donationAmount;
                $campaign->save();
                $donations->save();

                if ($campaign->total == 0) {
                    $campaign->state_campaign = 'اكتملت';
                    $campaign->save();
                }
            }

            $donations->user()->attach($request->user_id);
            $donations->campaign()->attach($request->campaign_id);


            return redirect('/campaign');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }





    public function edit($id)
    {

        $campaign = campaign::where('id', $id)->first();
        $categorie = categorie::all();
        $donation = donation::all();
        $users = User::all();

        return view('donations', compact('campaign', 'categorie', 'donation', 'users'));
    }


    public function update(Request $request, donation $donations)
    {
        //
    }


    public function destroy(donation $donations)
    {
        //
    }
}
