<?php

namespace App\Http\Controllers;

use App\Models\categorie;
use Illuminate\Http\Request;
use App\Http\Requests\CategoriesRequest;


class CategoriesController extends Controller
{

    public function index()
    {

        $categorie = categorie::latest()->get();
        return view('categories', compact('categorie'));
    }


    public function create()
    {
        //
    }


    public function store(CategoriesRequest $request)
{
    $input = $request->all();
    // تحقق من وجود ملف الصورة في الطلب
    if ($image =$request->File('image')) {
   
        $destinationPath = 'public/img/';
            $profileimage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileimage);
            $input['image'] = "$profileimage";
            categorie::create($input);
    } else {
        // إذا لم يكن هناك صورة، قم بإنشاء التصنيف بدون صورة

            categorie::create($input);
      
    }

    // إرسال رسالة نجاح إلى الجلسة
    session()->flash('Add', 'تم اضافة البيانات بنجاح ');
    
    // إعادة التوجيه إلى صفحة التصنيفات
    return redirect('/categories');
}


    public function show(categorie $categories)
    {
        //
    }


    public function edit(categorie $categories)
    {
        //
    }


    public function update(CategoriesRequest $request)
{
    // العثور على التصنيف من خلال المعرف
    $input = categorie::findOrFail($request->id);

    // التحقق من وجود صورة جديدة في الطلب
    if ($request->hasFile('image')) {
       
        
        // الحصول على الصورة من الطلب
        $image = $request->file('image');
        
        // تحديد اسم فريد للصورة لتجنب التعارض
        $destinationPath = 'public/img/';
        $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
        
        // تخزين الصورة في المسار المحدد
        $image->move($destinationPath, $profileImage);
        
        // تحديث مسار الصورة في قاعدة البيانات
        $input->image = $profileImage;
    } else {
     
        unset($input['image']);
    }

    // تحديث اسم التصنيف
    $input->name_category = $request->name_category;
    
    // حفظ التغييرات
    $input->save();

    // إرسال رسالة نجاح إلى الجلسة
    session()->flash('edit', 'تم تعديل البيانات بنجاح');
    
    // إعادة التوجيه إلى صفحة التصنيفات
    return redirect('/categories');
}



    public function destroy(Request $request, categorie $categories)
    {
        categorie::findOrFail($request->id)->delete();

        $categories->delete();
        session()->flash('delete', 'تم حذف البيانات بنجاج');
        return redirect('/categories');
    }
}
