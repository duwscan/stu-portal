<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo các quyền cơ bản
        $permissions = [
            'view users', 'create users', 'edit users', 'delete users',
            'view students', 'create students', 'edit students', 'delete students',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Tạo role admin và student
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $student = Role::firstOrCreate(['name' => 'student']);

        // Gán quyền cho admin
        $admin->syncPermissions($permissions);
        // Gán quyền cho student (ví dụ chỉ xem/sửa profile của mình)
        $student->syncPermissions(['view students', 'edit students']);
    }
}
