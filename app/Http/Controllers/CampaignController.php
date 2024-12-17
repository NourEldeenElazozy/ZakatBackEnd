<?php

namespace App\Http\Controllers;

use App\Models\campaign;
use App\Models\categorie;
use App\Models\donation;
use App\Models\User;
use App\Models\user_donation;
use App\Models\campaign_donation;
use App\Http\Requests\StoreCampaignRequest;
use App\Http\Requests\UpdateCampaignRequest;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use function Laravel\Prompts\select;

class CampaignController extends Controller
{


    public function index(Request $request)
    {

        $campaign = campaign::latest()->orderby('created_at', 'desc')->get();
        $categorie = categorie::latest()->get();



        return view('campaign', compact('campaign', 'categorie'));
    }


    public function create()
    {
        //
    }

    public function index2($id)
    {


        $donations = DB::table('donations')
            ->join('campaigns_donations', 'donations.id', '=', 'campaigns_donations.donation_id')
            ->join('campaigns', 'campaigns_donations.campaign_id', '=', 'campaigns.id')
            ->join('users_donations', 'donations.id', '=', 'users_donations.donation_id')
            ->join('users', 'users_donations.user_id', '=', 'users.id')
            ->where('campaigns.id', $id)
            ->select('donations.*', 'users.name as username', 'campaigns.name as campaign_name')
            ->get();

        return view('donations', compact('donations'));
    }


    public function store(StoreCampaignRequest $request)
    {


        $input = $request->all();

        // تحقق مما إذا كان total فارغًا
        if (empty($input['total'])) {
            $input['total'] = 0; // تعيين القيمة إلى 0
        }
    
        if ($image = $request->File('image')) {


            $destinationPath = 'public/img/';
            $profileimage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileimage);
            $input['image'] = "$profileimage";
        }

        campaign::create($input);

        session()->flash('Add', 'تم اضافة البيانات بنجاح ');
        return redirect('/campaign');
    }



    public function show($id)
    {
    }



    public function edit(campaign $campaigns)
    {
        //
    }


    public function update(UpdateCampaignRequest $request, campaign $campaigns)
    {


        $id = categorie::where('name_category', $request->name_category)->first()->id;

        $input = campaign::findOrFail($request->id);

        $input->name = $request->name;
        $input->description = $request->description;
        $input->categorie_id = $id;
        $input->image = $request->image;
        $input->total = $request->total;
        $input->paid_up = $request->paid_up;
        $input->recipient = $request->recipient;
        $input->state_campaign = $request->state_campaign;




        if ($image = $request->File('image')) {


            $destinationPath = 'public/img/';
            $profileimage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileimage);
            $input['image'] = "$profileimage";
        } else {
            unset($input['image']);
        }


        $input->save();

        session()->flash('edit', 'تم تعديل البيانات بنجاج');
        return redirect('/campaign');
    }


    public function destroy(Request $request, campaign $campaigns)
    {

        campaign::findOrFail($request->id)->delete();

        $campaigns->delete();
        session()->flash('delete', 'تم حذف البيانات بنجاج');
        return redirect('/campaign');
    }
}
