<?php

// app/Http/Controllers/RechargeCardController.php
namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RechargeCardsExport;
use App\Models\RechargeCard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RechargeCardController extends Controller
{
     private function generateNumericCode($length = 13)
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= random_int(0, 9);
        }
        return $code;
    }
    public function index()
    {
        $cards = RechargeCard::latest()->paginate(10);
        return view('recharge_cards.index', compact('cards'));
    }

    public function create()
    {
        return view('recharge_cards.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'value' => 'required|numeric|min:1',
            'quantity' => 'required|integer|min:1|max:100',
        ]);

    for ($i = 0; $i < $request->quantity; $i++) {
    RechargeCard::create([
        'code' => $this->generateNumericCode(13),
        'value' => $request->value,
    ]);
}


        return redirect()->route('recharge-cards.index')->with('success', 'تم إنشاء كروت الشحن بنجاح!');
    }

    public function export(Request $request)
{
    $value = $request->input('value'); // لتصفية حسب القيمة
    return Excel::download(new RechargeCardsExport($value), 'recharge_cards.xlsx');
}
    
}
