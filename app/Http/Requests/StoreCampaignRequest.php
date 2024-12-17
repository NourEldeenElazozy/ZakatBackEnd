<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \
     * \Contracts\Validation\ValidationRule|array<mixed>|string> 
     */

     public function rules(): array
     {
         return [
             'name' => 'required|regex:/^[\p{Arabic}\p{Latin} ]+$/u',
             'description' => 'required|regex:/^[\p{Arabic}\p{Latin} ]+$/u',
             'categorie_id' => 'required',
             'image' => 'required',
             'total' => 'nullable|numeric', // السماح بأن تكون فارغة
             'paid_up' => 'required|numeric',
             'recipient' => 'required|regex:/^[\p{Arabic}\p{Latin} ]+$/u',
             'state_campaign' => 'required|regex:/^[\p{Arabic}\p{Latin} ]+$/u',
         ];
     }



    public function messages(): array
    {
        return [

            'name.required' => 'يجب إدخال عنوان الحمله',
            'name.regex' => ' يجب إدخال عنوان الحمله نص',

            'description.required' => 'يجب إدخال الوصف ',
            'description.regex' => ' يجب إدخال الوصف كنص',

            'categorie_id.required' => 'يجب اختيار التصنيف   ',

            'image.required' => 'ادخل الصوره',

            'total.required' => 'يجب إدخال المبلغ ',
            'total.numeric' => '  يجب إدخال المبلغ كرقم',


            'paid_up.required' => 'يجب إدخال المدفوع ',
            'paid_up.numeric' => '  يجب إدخال المبلغ المدفوع كرقم',

            'recipient.required' => 'يجب إدخال المستفيد ',
            'recipient.regex' => ' يجب إدخال اسم المستفيد نص',

            'state_campaign.required' => 'يجب إدخال حالة الحمله ',
            'state_campaign.regex' => ' يجب إدخال اسم حالة الحمله نص',


        ];
    }
}
