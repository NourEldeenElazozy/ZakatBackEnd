<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = BankAccount::all();
        return view('bank_accounts.index', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'type' => 'required|in:zakat,sadaqah',
        ]);

        BankAccount::create($request->all());

        return redirect()->back()->with('success', 'تم إضافة الحساب بنجاح');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'type' => 'required|in:zakat,sadaqah',
        ]);

        $account = BankAccount::findOrFail($id);
        $account->update($request->all());

        return redirect()->back()->with('success', 'تم تعديل الحساب بنجاح');
    }

    public function destroy($id)
    {
        $account = BankAccount::findOrFail($id);
        $account->delete();

        return redirect()->back()->with('success', 'تم حذف الحساب بنجاح');
    }
}
