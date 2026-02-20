<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ZakatNisab;
use Illuminate\Http\Request;

class NisabController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
// جلب آخر صف من جدول ZakatNisab
        $lastRow = ZakatNisab::latest()->first();

        // التحقق مما إذا كان هناك صفوف في الجدول
        if ($lastRow) {
            return response()->json($lastRow, 200);
        } else {
            return response()->json(['message' => 'No records found'], 404);
        }
  
    }


}
