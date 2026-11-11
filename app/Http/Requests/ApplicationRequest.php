<?php

namespace App\Http\Requests;

use App\Forms\ApplicationSchema;
use Illuminate\Foundation\Http\FormRequest;

class ApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $input = $this->all();

        $rules = ['user_id' => ['nullable', 'integer', 'exists:users,id']];

        foreach (ApplicationSchema::all() as $pageKey => $page) {
            if ($pageKey === 'previous_addresses' && !ApplicationSchema::shouldShowPreviousAddresses($input)) {
                continue;
            }

            foreach ($page['fields'] ?? [] as $key => $field) {
                if ($field['type'] === 'heading') continue;
                if (!ApplicationSchema::isFieldVisible($key, $input)) continue;

                $fieldRules = [];
                $fieldRules[] = !empty($field['required']) ? 'required' : 'nullable';

                switch ($field['type']) {
                    case 'email':  $fieldRules[] = 'email'; break;
                    case 'number': $fieldRules[] = 'numeric'; break;
                    case 'date':   $fieldRules[] = 'date'; break;
                    case 'zip':    $fieldRules[] = 'regex:/^\d{5}$/'; break;
                    case 'phone':  $fieldRules[] = 'string'; break;
                }

                if (!empty($field['maxlength'])) {
                    $fieldRules[] = 'max:' . $field['maxlength'];
                }

                if (!empty($field['numeric'])) {
                    $fieldRules[] = 'regex:/^\d*$/';
                }

                $rules[$key] = $fieldRules;
            }
        }

        return $rules;
    }
}