<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoriesRequest extends FormRequest
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
            

            'name_category'=>'required|regex:/^[\p{Arabic}\p{Latin} ]+$/u',
            'image' => 'required',
        ];
    }



    public function messages(): array
    {
        return [

            'name_category.required'=>'يجب إدخال التصنيف ',   
            'name_category.regex'=>' يجب إدخال اسم التصنيف نص',  
            'image.required'=>'يجب إدخال صورة التصنيف ',  


        ];
    }
}
