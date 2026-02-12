<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            // Personal Information
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional(0.6)->firstName(),
            'last_name' => $this->faker->lastName(),
            'date_of_birth' => $this->faker->dateTimeBetween('-60 years', '-20 years')->format('Y-m-d'),
            'gender' => $this->faker->randomElement(['Male', 'Female', 'Other']),
            'marital_status' => $this->faker->randomElement(['Single', 'Married', 'Divorced', 'Widowed']),
            'nationality' => $this->faker->country(),

            // Contact Information
            'email' => $this->faker->unique()->safeEmail(),
            'phone_number' => $this->faker->phoneNumber(),
            'mobile_number' => $this->faker->phoneNumber(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_phone' => $this->faker->phoneNumber(),
            'emergency_contact_relationship' => $this->faker->randomElement(['Spouse', 'Parent', 'Sibling', 'Child', 'Friend']),

            // Address Information
            'street_address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'state_province' => $this->faker->state(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->country(),
            'residential_type' => $this->faker->randomElement(['Owned', 'Rented', 'With Family']),

            // Employment Information
            'employee_id_number' => 'EMP-' . $this->faker->unique()->numberBetween(1000, 9999),
            'department' => $this->faker->randomElement(['IT', 'HR', 'Finance', 'Operations', 'Marketing', 'Maintenance', 'Administration']),
            'position_title' => $this->faker->jobTitle(),
            'job_description' => $this->faker->paragraph(),
            'employment_status' => $this->faker->randomElement(['Active', 'Inactive', 'On Leave', 'Terminated']),
            'hire_date' => $this->faker->dateTimeBetween('-10 years', '-1 months')->format('Y-m-d'),
            'probation_end_date' => $this->faker->optional(0.3)->dateTimeBetween('-8 years', 'now')->format('Y-m-d'),
            'contract_type' => $this->faker->randomElement(['Full-time', 'Part-time', 'Contract', 'Temporary']),

            // Tax & Legal
            'tax_id_number' => $this->faker->numerify('###-##-####'),
            'id_document_type' => $this->faker->randomElement(['Passport', 'National ID', 'Driver License']),
            'id_document_number' => $this->faker->numerify('##########'),

            // Additional Information
            'manager_id' => null, // Will be set by relationships if needed
            'notes' => $this->faker->optional(0.3)->paragraph(),
            'profile_photo_path' => null,
            'start_date' => $this->faker->dateTimeBetween('-10 years', '-1 months')->format('Y-m-d'),
            'end_date' => null,
        ];
    }

    /**
     * State for creating an active employee
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'employment_status' => 'Active',
            'end_date' => null,
        ]);
    }

    /**
     * State for creating an inactive employee
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'employment_status' => 'Inactive',
        ]);
    }

    /**
     * State for creating a terminated employee
     */
    public function terminated(): static
    {
        return $this->state(fn (array $attributes) => [
            'employment_status' => 'Terminated',
            'end_date' => $this->faker->dateTime()->format('Y-m-d'),
        ]);
    }

    /**
     * State for creating a manager
     */
    public function manager(): static
    {
        return $this->state(fn (array $attributes) => [
            'position_title' => $this->faker->randomElement(['Department Manager', 'Senior Manager', 'Director']),
        ]);
    }
}
