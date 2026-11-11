<?php

namespace App\Http\Requests;

use App\Forms\QualificationSchema;
use Illuminate\Foundation\Http\FormRequest;

class QualificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $input = $this->all();
        $input['has_voucher'] = $this->boolean('has_voucher');

        $isVoucher = (bool) $input['has_voucher'];

        $applicablePages = $isVoucher
            ? QualificationSchema::voucherPath()
            : QualificationSchema::standardPath();

        $rules = [
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];

        foreach (QualificationSchema::all() as $pageKey => $page) {
            if (!in_array($pageKey, $applicablePages, true)) continue;

            foreach ($page['fields'] ?? [] as $key => $field) {
                // Saltar campos ocultos por condicionales
                if (!QualificationSchema::isFieldVisible($key, $input)) continue;

                $fieldRules = [];
                $fieldRules[] = !empty($field['required']) ? 'required' : 'nullable';

                switch ($field['type']) {
                    case 'email':       $fieldRules[] = 'email';   break;
                    case 'number':      $fieldRules[] = 'numeric'; break;
                    case 'date':        $fieldRules[] = 'date';    break;
                    case 'multiselect': $fieldRules[] = 'array';   break;
                }

                if (!empty($field['max'])) {
                    $fieldRules[] = 'max:' . $field['max'];
                }

                $rules[$key] = $fieldRules;

                if ($field['type'] === 'multiselect') {
                    $rules[$key . '.*'] = ['string'];
                }
            }
        }

        return $rules;
    }
}