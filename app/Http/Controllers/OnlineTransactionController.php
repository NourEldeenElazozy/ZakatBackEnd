<?php

namespace App\Http\Controllers;

use App\Models\EzonpayYusser;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OnlineTransactionsExport;

class OnlineTransactionController extends Controller
{
    public function index()
    {
        // جلب العمليات الناجحة وترتيبها تنازلياً حسب تاريخ الإنشاء
        $transactions = EzonpayYusser::orderBy('created_at', 'desc')->get();

        return view('reports.online_transactions', compact('transactions'));
    }

    public function exportExcel()
    {
        return Excel::download(new OnlineTransactionsExport, 'سجل_مدفوعات_أونلاين.xlsx');
    }
}