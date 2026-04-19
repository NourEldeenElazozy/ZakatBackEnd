<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DeveloperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التحقق مما إذا كان الإيميل موجوداً مسبقاً لتجنب تكرار الحساب
        $user = User::where('email', 'developer@yourdomain.com')->first();

        if (!$user) {
            // 1. إنشاء المستخدم أولاً وتخزينه في متغير
            $newUser = User::create([
                'name'     => 'Developer Admin',
                'email'    => 'developer@yourdomain.com',
                'password' => Hash::make('password'),
                'phone'    => '12345678',
            ]);

            // 2. التأكد من وجود الدور (Role) أو إنشائه إذا لم يكن موجوداً
            $role = Role::firstOrCreate(['name' => 'Admin']);

            // 3. جلب جميع الصلاحيات (Permissions) وربطها بالدور
            $permissions = Permission::pluck('id', 'id')->all();
            $role->syncPermissions($permissions);

            // 4. إسناد الدور (Role) إلى المستخدم الجديد
            $newUser->assignRole([$role->id]);

            $this->command->info('تم إنشاء حساب المطور وصلاحياته بنجاح!');
        } else {
            $this->command->info('حساب المطور موجود مسبقاً.');
        }
    }
}