<?php

namespace App\Http\Controllers;

use App\Models\ZakatNisab;
use Illuminate\Http\Request;

class ZakatNisabController extends Controller
{
    public function index()
    {
    $nisabs = ZakatNisab::all();
$latest = ZakatNisab::latest()->first();

return view('zakat_nisab', compact('nisabs', 'latest'));
    }

    public function create()
    {
        return view('zakat_nisab.create');
    }

  public function store(Request $request)
{
    $validated = $request->validate([
        'nisab_amount' => 'required|numeric',
        'nisab_24' => 'nullable|numeric',
        'nisab_21' => 'nullable|numeric',
        'nisab_18' => 'nullable|numeric',
        'price_24' => 'nullable|numeric',
        'price_21' => 'nullable|numeric',
        'price_18' => 'nullable|numeric',
        // الحقول الجديدة
        'kaffarat_yameen' => 'nullable|numeric',
        'fidyah_siyam' => 'nullable|numeric',
    ]);

    // تعيين last_updated إلى التاريخ الحالي
    $validated['last_updated'] = now();

    // إنشاء السجل الجديد في ZakatNisab
    ZakatNisab::create($validated);

    session()->flash('success', 'تم إضافة البيانات بنجاح.');
    return redirect('/zakat_nisab');
}
    public function destroy(Request $request)
    {
        
        ZakatNisab::findOrFail($request->id)->delete();

       
        session()->flash('delete', 'تم حذف البيانات بنجاج');
        return redirect('/zakat_nisab');
    }
}