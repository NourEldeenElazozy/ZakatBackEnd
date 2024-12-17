<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
   
     public function rules(): array
     {
         return [
 
 
             'name' => 'required|regex:/^[\p{Arabic}\p{Latin} ]+$/u',
             'description' => 'required|regex:/^[\p{Arabic}\p{Latin} ]+$/u',
             'name_category' => 'required',
             'image' => 'required',
             'total' => 'required|numeric',
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
 
             'name_category.required' => 'يجب اختيار التصنيف   ',
 
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
