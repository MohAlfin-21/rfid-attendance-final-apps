<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed roles and permissions.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Permissions ──────────────────────────────
        $permissions = [
            // User management
            'user.view', 'user.create', 'user.update', 'user.delete',

            // Classroom management
            'classroom.view', 'classroom.create', 'classroom.update', 'classroom.delete', 'classroom.manage-students',

            // Device management
            'device.view', 'device.create', 'device.update', 'device.delete', 'device.rotate-token',

            // Attendance
            'attendance.view-own', 'attendance.view-class', 'attendance.view-all',
            'attendance.manual-mark', 'attendance.override', 'attendance.export',

            // Absence requests
            'absence.create', 'absence.view-own', 'absence.view-all', 'absence.review',

            // System settings
            'settings.view', 'settings.update',

            // Dashboard
            'dashboard.admin', 'dashboard.teacher', 'dashboard.secretary', 'dashboard.student',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // ── Roles ────────────────────────────────────
        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions($permissions); // Admin gets everything

        $teacher = Role::findOrCreate('teacher', 'web');
        $teacher->syncPermissions([
            'classroom.view', 'classroom.manage-students',
            'attendance.view-class', 'attendance.manual-mark', 'attendance.export',
            'absence.view-all', 'absence.review',
            'dashboard.teacher',
        ]);

        $secretary = Role::findOrCreate('secretary', 'web');
        $secretary->syncPermissions([
            'user.view',
            'classroom.view',
            'attendance.view-all', 'attendance.export',
            'absence.view-all', 'absence.review', // Secretary can approve/reject absence
            'absence.create', 'absence.view-own',
            'dashboard.secretary', 'dashboard.student',
        ]);

        $student = Role::findOrCreate('student', 'web');
        $student->syncPermissions([
            'attendance.view-own',
            'absence.create', 'absence.view-own',
            'dashboard.student',
        ]);
    }
}
