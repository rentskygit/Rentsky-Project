<?php

namespace App\Forms;

class ApplicationSchema
{
    public static function all(): array
    {
        return [
            'rental_application' => self::rentalApplication(),
            'current_address' => self::currentAddress(),
            'previous_addresses' => self::previousAddresses(),
            'references' => self::references(),
            'occupants' => self::occupants(),
            'pets' => self::pets(),
            'employment' => self::employment(),
            'questions' => self::questions(),
        ];
    }

    public static function path(): array
    {
        return ['rental_application', 'current_address', 'previous_addresses', 'references', 'occupants', 'pets', 'employment', 'questions'];
    }

    protected static function rentalApplication(): array
    {
        return [
            'title' => 'Rental Application',
            'fields' => [
                'desired_move_in_heading' => [
                    'type' => 'heading',
                    'text' => 'Desired Move-in',
                ],
                'applicant_type' => [
                    'type' => 'single_check', 'label' => 'I am applying as:', 'required' => true,
                    'options' => [
                        'tenant' => 'I am applying as a tenant. (I will be living on the property.)',
                        'guarantor' => 'I am applying as a guarantor/co-signer for another applicant. (I will not be living on the property.)',
                    ],
                ],
                'contact_info_heading' => [
                    'type' => 'heading',
                    'text' => 'Your Contact Info',
                    'subtext' => 'Please let us know how we can best reach you.',
                ],
                'legal_first_name' => ['type' => 'text', 'label' => 'Legal First Name', 'required' => true],
                'no_middle_name' => ['type' => 'checkbox', 'label' => 'I certify I have no middle name'],
                'middle_name' => [
                    'type' => 'text', 'label' => 'Middle Name', 'required' => true,
                    'hide_when' => 'data.no_middle_name === true',
                ],
                'last_name' => ['type' => 'text', 'label' => 'Last Name', 'required' => true],
                'date_of_birth' => ['type' => 'date', 'label' => 'Date of Birth', 'required' => true],
                'ssn_last_4' => [
                    'type' => 'text',
                    'label' => 'Last 4 digits of your SSN or ITIN (if you do not have one, leave blank)',
                    'maxlength' => 4,
                    'numeric' => true,
                ],
                'phone_type' => [
                    'type' => 'select', 'label' => 'Type phone:', 'required' => true,
                    'options' => ['Home', 'Mobile', 'Office'],
                ],
                'phone' => ['type' => 'phone', 'label' => 'Phone Number', 'required' => true],
                'add_phone' => ['type' => 'checkbox', 'label' => 'Add Phone Number (Optional)'],
                'phone_2' => [
                    'type' => 'phone', 'label' => 'Phone Number',
                    'show_when' => 'data.add_phone === true',
                ],
                'email' => ['type' => 'email', 'label' => 'Email Address', 'required' => true],
                'how_heard' => ['type' => 'text', 'label' => 'How did you hear about us?', 'required' => true],
            ],
        ];
    }

    protected static function currentAddress(): array
    {
        return [
            'title' => 'Current Address',
            'description' => 'Please provide 5 years of residential history.',
            'fields' => [
                'current_address' => ['type' => 'text', 'label' => 'Address', 'required' => true],
                'current_apt' => ['type' => 'text', 'label' => 'Apt/Unit/Suite'],
                'current_city' => ['type' => 'text', 'label' => 'City', 'required' => true],
                'current_state' => ['type' => 'text', 'label' => 'State', 'required' => true],
                'current_zip' => ['type' => 'zip', 'label' => 'ZIP Code', 'required' => true],
                'resided_from' => ['type' => 'date', 'label' => 'Resided From', 'required' => true],
                'current_rent' => ['type' => 'number', 'prefix' => '$', 'label' => 'Monthly Rent', 'required' => true],
                'landlord_name' => ['type' => 'text', 'label' => 'Landlord Name', 'required' => true],
                'landlord_phone' => ['type' => 'phone', 'label' => 'Landlord Phone Number'],
                'landlord_email' => ['type' => 'email', 'label' => 'Landlord Email Address'],
                'reason_for_leaving' => ['type' => 'textarea', 'label' => 'Reason For Leaving', 'required' => true, 'rows' => 4],
            ],
        ];
    }

    protected static function previousAddresses(): array
    {
        return [
            'title' => 'Previous Addresses',
            'description' => 'Required when your current address is less than 5 years old.',
            'fields' => [
                'prev_address' => ['type' => 'text', 'label' => 'Address', 'required' => true],
                'prev_city' => ['type' => 'text', 'label' => 'City', 'required' => true],
                'prev_state' => ['type' => 'text', 'label' => 'State', 'required' => true],
                'prev_zip' => ['type' => 'zip', 'label' => 'ZIP Code', 'required' => true],
                'prev_resided_from' => ['type' => 'date', 'label' => 'Resided From', 'required' => true],
                'prev_rent' => ['type' => 'number', 'prefix' => '$', 'label' => 'Monthly Rent', 'required' => true],
                'prev_landlord_name' => ['type' => 'text', 'label' => 'Landlord Name'],
                'prev_landlord_phone' => ['type' => 'phone', 'label' => 'Landlord Phone Number'],
                'prev_landlord_email' => ['type' => 'email', 'label' => 'Landlord Email Address'],
                'prev_reason_for_leaving' => ['type' => 'textarea', 'label' => 'Reason For Leaving', 'required' => true, 'rows' => 4],
            ],
        ];
    }

    protected static function references(): array
    {
        return [
            'title' => 'Personal References & Emergency Contact',
            'fields' => [
                'ref_name' => ['type' => 'text', 'label' => 'Personal Reference — Name & Last Name', 'required' => true],
                'ref_address' => ['type' => 'text', 'label' => 'Full Address'],
                'ref_relationship' => ['type' => 'text', 'label' => 'Relationship (No Family Members)', 'required' => true],
                'ref_phone' => ['type' => 'phone', 'label' => 'Reference Phone Number', 'required' => true],
                'emergency_name' => ['type' => 'text', 'label' => 'Emergency Contact — Name & Last Name', 'required' => true],
                'emergency_address' => ['type' => 'text', 'label' => 'Address'],
                'emergency_relationship' => ['type' => 'text', 'label' => 'Relationship', 'required' => true],
                'emergency_phone' => ['type' => 'phone', 'label' => 'Emergency Phone Number', 'required' => true],
            ],
        ];
    }

    protected static function occupants(): array
    {
        return [
            'title' => 'Co-Applicants & Other Occupants',
            'description' => 'If you are applying with co-applicants, a separate application is required for each.',
            'fields' => [
                'has_co_applicants' => [
                    'type' => 'single_check', 'label' => 'I am applying with Co-Applicants', 'required' => true,
                    'options' => ['Yes' => 'Yes', 'No' => 'No'],
                ],
                'has_adults_over_18' => [
                    'type' => 'single_check', 'label' => 'I will have additional occupants living with me OVER 18 years old NOT working', 'required' => true,
                    'options' => ['Yes' => 'Yes', 'No' => 'No'],
                ],
                'over_18_names' => [
                    'type' => 'text', 'label' => 'Name/Names', 'required' => true,
                    'show_when' => 'data.has_adults_over_18 === "Yes"',
                ],
                'over_18_relationship' => [
                    'type' => 'text', 'label' => 'Relationship', 'required' => true,
                    'show_when' => 'data.has_adults_over_18 === "Yes"',
                ],
                'has_minors_under_18' => [
                    'type' => 'single_check', 'label' => 'I will have additional occupants living with me UNDER 18 years old', 'required' => true,
                    'options' => ['Yes' => 'Yes', 'No' => 'No'],
                ],
                'under_18_names_age' => [
                    'type' => 'text', 'label' => 'Name/Names & Age', 'required' => true,
                    'show_when' => 'data.has_minors_under_18 === "Yes"',
                ],
                'under_18_dob' => [
                    'type' => 'date', 'label' => 'Date of Birth', 'required' => true,
                    'show_when' => 'data.has_minors_under_18 === "Yes"',
                ],
                'under_18_relationship' => [
                    'type' => 'text', 'label' => 'Relationship', 'required' => true,
                    'show_when' => 'data.has_minors_under_18 === "Yes"',
                ],
            ],
        ];
    }

    protected static function pets(): array
    {
        return [
            'title' => 'Pets',
            'fields' => [
                'has_pets' => [
                    'type' => 'single_check', 'label' => 'I have pets', 'required' => true,
                    'options' => ['Yes' => 'Yes', 'No' => 'No'],
                ],
                'pet_name' => [
                    'type' => 'text', 'label' => "Pet's Name", 'required' => true,
                    'show_when' => 'data.has_pets === "Yes"',
                ],
                'pet_type' => [
                    'type' => 'text', 'label' => 'Type/Breed', 'required' => true,
                    'show_when' => 'data.has_pets === "Yes"',
                ],
                'pet_weight' => [
                    'type' => 'number', 'label' => 'Weight (lbs)', 'required' => true, 'min' => 0,
                    'show_when' => 'data.has_pets === "Yes"',
                ],
                'pet_age' => [
                    'type' => 'text', 'label' => 'Age', 'required' => true,
                    'show_when' => 'data.has_pets === "Yes"',
                ],
            ],
        ];
    }

    protected static function employment(): array
    {
        return [
            'title' => 'Employment & Income',
            'fields' => [
                'employer_name' => ['type' => 'text', 'label' => 'Employer Name', 'required' => true],
                'employer_address' => ['type' => 'text', 'label' => 'Address', 'required' => true],
                'employer_city' => ['type' => 'text', 'label' => 'City', 'required' => true],
                'employer_state' => ['type' => 'text', 'label' => 'State', 'required' => true],
                'employer_zip' => ['type' => 'zip', 'label' => 'ZIP Code', 'required' => true],
                'employer_phone' => ['type' => 'phone', 'label' => 'Employer Phone Number', 'required' => true],
                'monthly_salary' => ['type' => 'number', 'prefix' => '$', 'label' => 'Monthly Salary', 'required' => true],
                'position_held' => ['type' => 'text', 'label' => 'Position Held', 'required' => true],
                'years_worked' => [
                    'type' => 'select', 'label' => 'Years Worked', 'required' => true,
                    'options' => ['1', '2', '3', '4', '5', '6', '7', '8', '9', '+10'],
                ],
                'supervisor_name' => ['type' => 'text', 'label' => 'Supervisor Name', 'required' => true],
                'supervisor_phone' => ['type' => 'phone', 'label' => 'Supervisor Phone Number', 'required' => true],
                'supervisor_email' => ['type' => 'email', 'label' => 'Supervisor Email Address'],
                'additional_income' => ['type' => 'number', 'prefix' => '$', 'label' => 'Additional Monthly Income'],
                'additional_income_source' => ['type' => 'text', 'label' => 'Source'],
            ],
        ];
    }

    protected static function questions(): array
    {
        return [
            'title' => 'Questions & Signature',
            'fields' => [
                'had_felony' => [
                    'type' => 'single_check', 'label' => 'Have you ever been convicted of a felony?', 'required' => true,
                    'options' => ['Yes' => 'Yes', 'No' => 'No'],
                ],
                'felony_explanation' => [
                    'type' => 'textarea', 'label' => 'Please explain:', 'required' => true, 'rows' => 3,
                    'show_when' => 'data.had_felony === "Yes"',
                ],
                'had_housing_court' => [
                    'type' => 'single_check', 'label' => 'Have you ever been in housing court?', 'required' => true,
                    'options' => ['Yes' => 'Yes', 'No' => 'No'],
                ],
                'housing_court_explanation' => [
                    'type' => 'textarea', 'label' => 'Please explain:', 'required' => true, 'rows' => 3,
                    'show_when' => 'data.had_housing_court === "Yes"',
                ],
                'had_eviction_suit' => [
                    'type' => 'single_check', 'label' => 'Have you ever been sued for eviction?', 'required' => true,
                    'options' => ['Yes' => 'Yes', 'No' => 'No'],
                ],
                'eviction_suit_explanation' => [
                    'type' => 'textarea', 'label' => 'Please explain:', 'required' => true, 'rows' => 3,
                    'show_when' => 'data.had_eviction_suit === "Yes"',
                ],
                'had_bills_suit' => [
                    'type' => 'single_check', 'label' => 'Have you ever been sued for bills?', 'required' => true,
                    'options' => ['Yes' => 'Yes', 'No' => 'No'],
                ],
                'bills_suit_explanation' => [
                    'type' => 'textarea', 'label' => 'Please explain:', 'required' => true, 'rows' => 3,
                    'show_when' => 'data.had_bills_suit === "Yes"',
                ],
                'signature' => [
                    'type' => 'text', 'label' => 'By typing your name below, you certify that the information provided is true and complete. — Signature', 'required' => true,
                ],
                'signature_date' => [
                    'type' => 'date', 'label' => 'Date', 'required' => true,
                    'auto_today' => true,
                ],
            ],
        ];
    }

    public static function isFieldVisible(string $key, array $data): bool
    {
        $rules = [
            'middle_name' => fn($d) => empty($d['no_middle_name']),
            'phone_2' => fn($d) => !empty($d['add_phone']),

            'over_18_names' => fn($d) => ($d['has_adults_over_18'] ?? '') === 'Yes',
            'over_18_relationship' => fn($d) => ($d['has_adults_over_18'] ?? '') === 'Yes',

            'under_18_names_age' => fn($d) => ($d['has_minors_under_18'] ?? '') === 'Yes',
            'under_18_dob' => fn($d) => ($d['has_minors_under_18'] ?? '') === 'Yes',
            'under_18_relationship' => fn($d) => ($d['has_minors_under_18'] ?? '') === 'Yes',

            'pet_name' => fn($d) => ($d['has_pets'] ?? '') === 'Yes',
            'pet_type' => fn($d) => ($d['has_pets'] ?? '') === 'Yes',
            'pet_weight' => fn($d) => ($d['has_pets'] ?? '') === 'Yes',
            'pet_age' => fn($d) => ($d['has_pets'] ?? '') === 'Yes',

            'felony_explanation' => fn($d) => ($d['had_felony'] ?? '') === 'Yes',
            'housing_court_explanation' => fn($d) => ($d['had_housing_court'] ?? '') === 'Yes',
            'eviction_suit_explanation' => fn($d) => ($d['had_eviction_suit'] ?? '') === 'Yes',
            'bills_suit_explanation' => fn($d) => ($d['had_bills_suit'] ?? '') === 'Yes',
        ];

        if (!isset($rules[$key])) return true;
        return $rules[$key]($data);
    }

    public static function shouldShowPreviousAddresses(array $data): bool
    {
        $resided = $data['resided_from'] ?? null;
        if (!$resided) return false;

        try {
            $date = \Carbon\Carbon::parse($resided);
            return $date->greaterThan(now()->subYears(5));
        } catch (\Throwable $e) {
            return false;
        }
    }
}