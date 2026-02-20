<?php

namespace App\Exports;

use App\Models\RechargeCard;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class RechargeCardsExport implements FromCollection, WithHeadings
{
    protected $value;

    public function __construct($value = null)
    {
        $this->value = $value;
    }

    public function collection()
    {
        $query = RechargeCard::query();

        if ($this->value) {
            $query->where('value', $this->value);
        }

        return $query->get(['code', 'value', 'used', 'created_at']);
    }

    public function headings(): array
    {
        return ['رقم الكارت', 'القيمة', 'الحالة', 'تاريخ الإنشاء'];
    }
}