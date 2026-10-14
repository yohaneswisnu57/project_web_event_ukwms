<?php

namespace Database\Factories;

use App\Enums\ParticipantOrigin;
use App\Enums\ParticipantStatus;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Participant>
 */
class ParticipantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'whatsapp' => '08'.fake()->unique()->numerify('##########'),
            'origin' => ParticipantOrigin::Internal,
            'institution' => null,
            'faculty' => 'Kedokteran',
            'student_number' => fake()->unique()->numerify('19########'),
            'cohort_year' => fake()->numberBetween(2021, 2025),
            'food_allergy' => null,
            'payment_proof_path' => Participant::PAYMENT_PROOF_DIRECTORY.'/'.fake()->uuid().'.jpg',
            'status' => ParticipantStatus::Pending,
        ];
    }

    public function external(): static
    {
        return $this->state(fn (array $attributes): array => [
            'origin' => ParticipantOrigin::External,
            'institution' => fake()->randomElement(['Universitas Airlangga', 'Universitas Ciputra', 'Universitas Hang Tuah']),
        ]);
    }

    public function withAllergy(string $allergy = 'Udang'): static
    {
        return $this->state(fn (array $attributes): array => [
            'food_allergy' => $allergy,
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ParticipantStatus::Verified,
            'reviewed_at' => now(),
            'reviewed_by' => User::factory(),
        ]);
    }

    public function rejected(string $reason = 'Nominal transfer tidak sesuai.'): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ParticipantStatus::Rejected,
            'rejection_reason' => $reason,
            'reviewed_at' => now(),
            'reviewed_by' => User::factory(),
        ]);
    }

    public function checkedIn(): static
    {
        return $this->verified()->state(fn (array $attributes): array => [
            'checked_in_at' => now(),
        ]);
    }
}
