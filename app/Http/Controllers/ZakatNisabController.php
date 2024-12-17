<?php

namespace App\Http\Controllers;

use App\Models\ZakatNisab;
use Illuminate\Http\Request;

class ZakatNisabController extends Controller
{
    public function index()
    {
        $nisabs = ZakatNisab::all();
        return view('zakat_nisab', compact('nisabs'));
    }

    public function create()
    {
        return view('zakat_nisab.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisab_amount' => 'required|numeric',
            // لا تحتاج إلى التحقق من 'last_updated' هنا
        ]);
    
        // تعيين last_updated إلى التاريخ الحالي
        $validated['last_updated'] = now();
    
        // إنشاء السجل الجديد في ZakatNisab
        ZakatNisab::create($validated);
    
        session()->flash('success', 'تم إضافة نصاب الزكاة بنجاح.');
        return redirect('/zakat_nisab');
    }
    public function destroy(Request $request)
    {
        
        ZakatNisab::findOrFail($request->id)->delete();

       
        session()->flash('delete', 'تم حذف البيانات بنجاج');
        return redirect('/zakat_nisab');
    }
}