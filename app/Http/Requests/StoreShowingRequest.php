<?php

namespace App\Http\Requests;

use App\Models\Showing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'property_id'  => ['required', 'integer', 'exists:properties,id'],
            'showing_date' => ['required', 'date', 'after_or_equal:today'],
            'showing_time' => ['required', Rule::in([
                '8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM', '12:00 PM',
                '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM',
                '6:00 PM', '7:00 PM', '8:00 PM',
            ])],
        ];
    }

    public function messages(): array
    {
        return [
            'showing_date.after_or_equal' => 'The showing date must be today or later.',
            'showing_time.in'             => 'Please select a valid showing time.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $user = auth()->user();
            if (!$user || !$user->has_qualification) {
                $v->errors()->add('qualification', 'You must complete your qualification before scheduling a showing.');
                return;
            }

            $date = $this->input('showing_date');
            if ($date) {
                $count = Showing::where('user_id', $user->id)
                    ->where('showing_date', $date)
                    ->count();

                if ($count >= 3) {
                    $v->errors()->add('showing_date', 'You can only schedule 3 showings per day.');
                }
            }
        });
    }
}