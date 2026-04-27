<?php

namespace Database\Factories;

use App\Models\RegistrationAttendee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RegistrationAttendee>
 */
class RegistrationAttendeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'registration_id' => \App\Models\Registration::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'position' => $this->faker->jobTitle(),
        ];
    }
}
