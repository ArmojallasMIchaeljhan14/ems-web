<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Anyone authenticated can create/update their own event
        return auth()->check();
    }

    public function rules(): array
    {
        $rules = [
            // Basic event fields
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'start_at'    => ['required', 'date', 'after:now'],
            'end_at'      => ['required', 'date', 'after:start_at'],
            'venue_id'    => ['required', 'exists:venues,id'],

            // Nested arrays
            'resources'          => ['nullable', 'array'],
            'resources.*'        => ['nullable', 'integer', 'min:0'],
            'committee'          => ['nullable', 'array'],
            'committee.*.employee_id' => ['nullable', 'exists:employees,id'],
            'committee.*.role'       => ['nullable', 'string', 'max:100'],
            'budget_items'       => ['nullable', 'array'],
            'budget_items.*.description' => ['nullable', 'string', 'max:255'],
            'budget_items.*.amount'      => ['nullable', 'numeric', 'min:0'],
        ];

        // Admin-specific rules for editing statuses
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['status'] = ['required', 'string', 'in:pending_approvals,approved,rejected,published,cancelled,completed'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Event title is required.',
            'description.required' => 'Event description is required.',
            'start_at.required' => 'Start date & time is required.',
            'start_at.after' => 'Event start time must be in the future.',
            'end_at.required' => 'End date & time is required.',
            'end_at.after' => 'Event end time must be after start time.',
            'venue_id.required' => 'Please select a venue.',
            'venue_id.exists' => 'Selected venue does not exist.',
            'resources.array' => 'Invalid resources format.',
            'resources.*.integer' => 'Resource quantity must be a number.',
            'resources.*.min' => 'Resource quantity cannot be negative.',
            'committee.array' => 'Invalid committee format.',
            'committee.*.employee_id.exists' => 'Selected employee does not exist.',
            'committee.*.role.max' => 'Role name is too long (max 100 characters).',
            'budget_items.array' => 'Invalid budget items format.',
            'budget_items.*.description.max' => 'Budget description too long (max 255).',
            'budget_items.*.amount.numeric' => 'Budget amount must be numeric.',
            'budget_items.*.amount.min' => 'Budget amount cannot be negative.',
            'status.in' => 'Invalid event status.',
        ];
    }
}
