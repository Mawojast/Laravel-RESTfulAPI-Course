<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $password;
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => $password ?: $password = bcrypt('secret'), // password
            'remember_token' => Str::random(10),
            'verified' => fake()->randomElement([User::VERIFIED, User::UNVERIFIED]),
            //'verified' => $verfied = fake()->randomElement([User::VERIFIED, User::UNVERIFIED]),
            'verification_token' =>  User::generateVerificationCode(),
            'admin' => $verified = fake()->randomElement([User::ADMIN, User::REGULAR_USER]),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
