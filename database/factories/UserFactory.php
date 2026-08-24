<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'phone' => fake()->e164PhoneNumber(),
            'birthday' => fake()->dateTimeBetween('-60 years', '-10 years')->format('Y-m-d'),
            'address' => fake()->address(),
            'avatar' => 'images/avatar/basic-avatar.png',
            'personal_info' => fake()->paragraph(),
            'working_place' => fake()->company(),
            'grade' => User::GRADE_14,
            'role' => User::ROLE_STUDENT,
            'is_admin' => false,
            'instructor_rate' => 0,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (): array => ['email_verified_at' => null]);
    }

    public function instructor(): static
    {
        return $this->state(fn (): array => [
            'avatar' => 'images/default_avatar/teacher.jpg',
            'role' => User::ROLE_TEACHER,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (): array => [
            'avatar' => 'images/default_avatar/admin.jpg',
            'role' => User::ROLE_ADMIN,
            'is_admin' => true,
        ]);
    }
}
