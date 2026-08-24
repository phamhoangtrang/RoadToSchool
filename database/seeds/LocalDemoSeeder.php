<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LocalDemoSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'RoadToSchool Admin',
                'email' => 'admin@roadtoschool.local',
                'email_verified_at' => $now,
                'password' => Hash::make('123456'),
                'phone' => '0900000001',
                'address' => 'Ha Noi',
                'avatar' => 'images/default_avatar/admin.jpg',
                'personal_info' => 'Local administrator account.',
                'working_place' => 'RoadToSchool',
                'grade' => 14,
                'role' => 0,
                'is_admin' => 1,
                'instructor_rate' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Demo Instructor',
                'email' => 'instructor@roadtoschool.local',
                'email_verified_at' => $now,
                'password' => Hash::make('123456'),
                'phone' => '0900000002',
                'address' => 'Ho Chi Minh City',
                'avatar' => 'images/default_avatar/teacher.jpg',
                'personal_info' => 'Instructor account used for local development.',
                'working_place' => 'RoadToSchool Academy',
                'grade' => 13,
                'role' => 1,
                'is_admin' => 0,
                'instructor_rate' => 4.8,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Demo Student',
                'email' => 'student@roadtoschool.local',
                'email_verified_at' => $now,
                'password' => Hash::make('123456'),
                'phone' => '0900000003',
                'address' => 'Da Nang',
                'avatar' => 'images/default_avatar/student.jpeg',
                'personal_info' => 'Student account used for local development.',
                'working_place' => 'RoadToSchool',
                'grade' => 12,
                'role' => 2,
                'is_admin' => 0,
                'instructor_rate' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('categories')->insert([
            ['id' => 1, 'title' => 'Development', 'vi_title' => 'Phát triển', 'parent_id' => 0, 'css_classes' => 'lnr lnr-code color-8', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'title' => 'IT & Software', 'vi_title' => 'IT & Phần mềm', 'parent_id' => 0, 'css_classes' => 'lnr lnr-laptop-phone color-2', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'title' => 'Web Development', 'vi_title' => 'Phát triển web', 'parent_id' => 1, 'css_classes' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'title' => 'Programming Languages', 'vi_title' => 'Ngôn ngữ lập trình', 'parent_id' => 1, 'css_classes' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'title' => 'Data Science', 'vi_title' => 'Khoa học dữ liệu', 'parent_id' => 2, 'css_classes' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('courses')->insert([
            [
                'id' => 1,
                'title' => 'Machine Learning Fundamentals',
                'course_avatar' => 'images/default_course_avatar/machine_learning_1.jpg',
                'course_avatar_2' => 'images/default_course_avatar/machine_learning_2.jpg',
                'course_avatar_3' => 'images/default_course_avatar/machine_learning_3.jpeg',
                'description' => '<p>Learn the foundations of supervised and unsupervised machine learning through practical examples.</p>',
                'origin_price' => 399.99,
                'promotion_price' => 199.99,
                'lecture_numbers' => 3,
                'duration' => 95,
                'seller' => 248,
                'level' => 2,
                'course_rate' => 4.8,
                'is_accepted' => 1,
                'category_id' => 5,
                'user_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'title' => 'Laravel Web Development',
                'course_avatar' => 'images/default_course_avatar/laravel1.jpg',
                'course_avatar_2' => 'images/default_course_avatar/laravel2.png',
                'course_avatar_3' => 'images/default_course_avatar/laravel3.jpeg',
                'description' => '<p>Build a complete server-side web application with Laravel, routing, Blade and Eloquent.</p>',
                'origin_price' => 189.99,
                'promotion_price' => 79.99,
                'lecture_numbers' => 2,
                'duration' => 70,
                'seller' => 176,
                'level' => 2,
                'course_rate' => 4.5,
                'is_accepted' => 1,
                'category_id' => 3,
                'user_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'title' => 'Modern JavaScript Essentials',
                'course_avatar' => 'images/default_course_avatar/es6.jpeg',
                'course_avatar_2' => 'images/default_course_avatar/es6_2.png',
                'course_avatar_3' => 'images/default_course_avatar/es6_3.jpeg',
                'description' => '<p>A concise introduction to modern JavaScript syntax and browser development.</p>',
                'origin_price' => 99.99,
                'promotion_price' => 49.99,
                'lecture_numbers' => 1,
                'duration' => 45,
                'seller' => 112,
                'level' => 1,
                'course_rate' => 4.2,
                'is_accepted' => 1,
                'category_id' => 4,
                'user_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        DB::table('lectures')->insert([
            ['id' => 1, 'title' => 'Welcome to Machine Learning', 'description' => 'Course introduction and learning objectives.', 'video_link' => 'https://www.youtube.com/watch?v=PNp1prcWbkM', 'duration' => '00:10:00', 'week' => 1, 'index' => 0, 'is_lecture' => 1, 'is_quiz' => 0, 'is_accepted' => 1, 'course_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'title' => 'Supervised Learning', 'description' => 'Regression and classification concepts.', 'video_link' => 'https://www.youtube.com/watch?v=LslruK1p-70', 'duration' => '00:25:00', 'week' => 1, 'index' => 1, 'is_lecture' => 1, 'is_quiz' => 0, 'is_accepted' => 1, 'course_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'title' => 'Machine Learning Quiz', 'description' => 'Check your understanding of the fundamentals.', 'video_link' => null, 'duration' => '00:15:00', 'week' => 1, 'index' => 2, 'is_lecture' => 0, 'is_quiz' => 1, 'is_accepted' => 1, 'course_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'title' => 'Laravel Routing and Controllers', 'description' => 'Understand the request lifecycle in Laravel.', 'video_link' => 'https://www.youtube.com/watch?v=XJwhQumKCxU', 'duration' => '00:30:00', 'week' => 1, 'index' => 0, 'is_lecture' => 1, 'is_quiz' => 0, 'is_accepted' => 1, 'course_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'title' => 'Blade and Eloquent', 'description' => 'Render views and work with relational data.', 'video_link' => 'https://www.youtube.com/watch?v=AbCsV68Kzrg', 'duration' => '00:40:00', 'week' => 1, 'index' => 1, 'is_lecture' => 1, 'is_quiz' => 0, 'is_accepted' => 1, 'course_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'title' => 'ES6 Overview', 'description' => 'Variables, functions, modules and async JavaScript.', 'video_link' => 'https://www.youtube.com/watch?v=NCwa_xi0Uuc', 'duration' => '00:45:00', 'week' => 1, 'index' => 0, 'is_lecture' => 1, 'is_quiz' => 0, 'is_accepted' => 1, 'course_id' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('quiz_elements')->insert([
            ['id' => 1, 'content' => 'Which type of learning uses labelled training data?', 'is_question' => 1, 'is_multiple_choice' => 0, 'is_answer' => 0, 'question_parent_id' => null, 'is_right_answer' => null, 'lecture_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'content' => 'Supervised learning', 'is_question' => 0, 'is_multiple_choice' => null, 'is_answer' => 1, 'question_parent_id' => 1, 'is_right_answer' => 1, 'lecture_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'content' => 'Unsupervised learning', 'is_question' => 0, 'is_multiple_choice' => null, 'is_answer' => 1, 'question_parent_id' => 1, 'is_right_answer' => 0, 'lecture_id' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('course_user')->insert([
            'course_id' => 1,
            'user_id' => 3,
            'rate' => 5,
            'appreciate' => 'A clear and useful introduction.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('processes')->insert([
            ['status' => 1, 'lecture_id' => 1, 'user_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['status' => 0, 'lecture_id' => 2, 'user_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['status' => 0, 'lecture_id' => 3, 'user_id' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('cart_items')->insert([
            ['cart_item_type' => 0, 'course_id' => 2, 'user_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['cart_item_type' => 2, 'course_id' => 3, 'user_id' => 3, 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('comments')->insert([
            'content' => 'This demo course is easy to follow.',
            'parent_comment' => null,
            'course_id' => 1,
            'user_id' => 3,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $permissions = [
            ['Access admin dashboard', 0],
            ['Show all bill', 0],
            ['Show a bill', 0],
            ['Update a bill status', 0],
            ['Update user permission', 0],
            ['Show user permission', 0],
            ['Add new permission', 0],
            ['View all course information', 0],
            ['View all categories', 0],
            ['View all lecture requests', 0],
            ['Accept a lecture request', 0],
            ['View all users', 0],
            ['View instructor ranking', 0],
            ['Create new instructor', 0],
            ['Check and reply all conversation waiting', 0],
            ['Update an user', 0],
            ['Active a course', 0],
            ['Access instructor dashboard', 1],
            ["Show instructor's course", 1],
            ["Show a instructor's course", 1],
            ['Create a lecture in course', 1],
            ['Create a course', 1],
            ['Access page', 2],
            ['Show profile', 2],
            ['Show all courses', 2],
            ['Show a course', 2],
            ['Show a lecture', 2],
        ];

        foreach ($permissions as $permission) {
            $permissionId = DB::table('permissions')->insertGetId([
                'content' => $permission[0],
                'group_permission' => $permission[1],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('permission_user')->insert([
                'permission_id' => $permissionId,
                'user_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($permission[1] === 1) {
                DB::table('permission_user')->insert([
                    'permission_id' => $permissionId,
                    'user_id' => 2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
