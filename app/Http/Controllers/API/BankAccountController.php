<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = BankAccount::all();

        $zakatAccounts = $accounts->where('type', 'zakat')->values();
        $sadaqahAccounts = $accounts->where('type', 'sadaqah')->values();

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الحسابات بنجاح',
            'data' => [
                'zakat_accounts' => $zakatAccounts->map(function($account) {
                    return [
                        'id' => $account->id,
                        'bank_name' => $account->bank_name,
                        'account_number' => $account->account_number
                    ];
                }),
                'sadaqah_accounts' => $sadaqahAccounts->map(function($account) {
                    return [
                        'id' => $account->id,
                        'bank_name' => $account->bank_name,
                        'account_number' => $account->account_number
                    ];
                })
            ]
        ]);
    }
}
