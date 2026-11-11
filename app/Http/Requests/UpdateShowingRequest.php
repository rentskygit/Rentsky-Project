<?php

namespace App\Http\Requests;

use App\Models\Showing;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'showing_date' => ['required', 'date', 'after_or_equal:today'],
            'showing_time' => ['required', Rule::in([
                '8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM', '12:00 PM',
                '1:00 PM', '2:00 PM', '3:00 PM', '4:00 PM', '5:00 PM',
                '6:00 PM', '7:00 PM', '8:00 PM',
            ])],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            /** @var Showing $showing */
            $showing = $this->route('showing');
            $date = $this->input('showing_date');
            if (!$showing || !$date) return;

            $count = Showing::where('user_id', $showing->user_id)
                ->where('showing_date', $date)
                ->where('id', '!=', $showing->id)
                ->count();

            if ($count >= 3) {
                $v->errors()->add('showing_date', 'This client already has 3 showings on that day.');
            }
        });
    }
}