<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Identity is asserted by the gateway (auth.gateway middleware)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'start_at' => ['required', 'date', 'after:now'],
            'end_at' => ['nullable', 'date', 'after:start_at'],
            'provider_id' => ['nullable', 'integer', 'exists:user_snapshots,user_id'],
        ];
    }
}
