<?php

namespace App\Support;

final class RoutePermissions
{
    /** @var array<string, string> */
    public const MAP = [
        'admin.dashboard' => 'Access admin dashboard',

        'admin.bills.index' => 'Show all bill',
        'admin.bills.create' => 'Show all bill',
        'admin.bills.show' => 'Show a bill',
        'admin.bills.updateStatus' => 'Update a bill status',

        'admin.permissions.index' => 'Show user permission',
        'admin.permissions.getPermission' => 'Show user permission',
        'admin.permissions.updatePermission' => 'Update user permission',
        'admin.permissions.create' => 'Add new permission',
        'admin.permissions.store' => 'Add new permission',

        'admin.courses.index' => 'View all course information',
        'admin.courses.show' => 'View all course information',
        'admin.courses.edit' => 'View all course information',
        'admin.courses.update' => 'View all course information',
        'admin.courses.destroy' => 'View all course information',
        'admin.active-course' => 'Active a course',

        'admin.categories.index' => 'View all categories',
        'admin.categories.store' => 'View all categories',
        'admin.categories.show' => 'View all categories',
        'admin.categories.edit' => 'View all categories',
        'admin.categories.update' => 'View all categories',
        'admin.categories.destroy' => 'View all categories',

        'admin.lectures.requests.index' => 'View all lecture requests',
        'admin.lectures.requests.accept' => 'Accept a lecture request',

        'admin.users.index' => 'View all users',
        'admin.users.show' => 'View all users',
        'admin.users.edit' => 'View all users',
        'admin.users.destroy' => 'View all users',
        'admin.users.update' => 'Update an user',
        'admin.instructor_ranking' => 'View instructor ranking',
        'admin.users.create_instructor' => 'Create new instructor',
        'admin.users.store_new_instructor' => 'Create new instructor',

        'admin.conversations.waiting' => 'Check and reply all conversation waiting',

        'instructor.dashboard' => 'Access instructor dashboard',
        'instructor.courses.index' => "Show instructor's course",
        'instructor.courses.show' => "Show a instructor's course",
        'instructor.courses.create' => 'Create a course',
        'instructor.courses.store' => 'Create a course',
        'instructor.courses.lectures.create' => 'Create a lecture in course',
        'instructor.courses.lectures.store' => 'Create a lecture in course',
    ];

    public static function forRoute(?string $routeName): ?string
    {
        return $routeName ? (self::MAP[$routeName] ?? null) : null;
    }
}
