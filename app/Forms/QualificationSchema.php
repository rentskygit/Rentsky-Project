<?php

namespace App\Forms;

class QualificationSchema
{
    public static function all(): array
    {
        return [
            'contact'    => self::contact(),
            'housing'    => self::housing(),
            'household'  => self::household(),
            'employment' => self::employment(),
            'background' => self::background(),
            'voucher'    => self::voucher(),
            'showing'    => self::showing(),
        ];
    }

    public static function standardPath(): array
    {
        return ['contact', 'housing', 'household', 'employment', 'background', 'showing'];
    }


    public static function voucherPath(): array
    {
        return ['contact', 'voucher', 'showing'];
    }


    protected static function contact(): array
    {
        return [
            'title'       => 'Contact Information',
            'description' => 'This form is made in order to obtain the necessary information to schedule a showing in the places available by JCM Realty Group.',
            'fields' => [
                'name' => [
                    'type' => 'text', 'label' => 'What is your name?', 'required' => true,
                ],
                'last_name' => [
                    'type' => 'text', 'label' => 'Last name?', 'required' => true,
                ],
                'phone' => [
                    'type' => 'phone', 'label' => 'Enter your phone number', 'required' => true,
                ],
                'email' => [
                    'type' => 'email', 'label' => 'Enter your email address', 'required' => true,
                ],
                'cities' => [
                    'type' => 'multiselect', 'label' => 'Which cities are you looking for an apartment in? (Please write all that apply.)',
                    'required' => true,
                    'options' => self::maCities(),
                ],
                'has_voucher' => [
                    'type' => 'checkbox',
                    'label' => 'I have a housing voucher',
                    'help'  => 'The reason for this question is that the income requirements are different.',
                ],
                'voucher_type' => [
                    'type' => 'select', 'label' => 'What type of voucher do you have?',
                    'required' => true,
                    'options' => self::voucherTypes(),
                    'show_when' => 'data.has_voucher === true',
                ],
            ],
        ];
    }

    protected static function housing(): array
    {
        return [
            'title' => 'Housing Needs & Preferences',
            'fields' => [
                'bedrooms' => [
                    'type' => 'select', 'label' => 'How many bedrooms do you need?', 'required' => true,
                    'options' => ['0 / Studio','1','2','3','4','+5'],
                ],
                'rent_budget' => [
                    'type' => 'select', 'label' => 'What is your maximum monthly rent budget?', 'required' => true,
                    'options' => ['Under $1,000','$1,000 to $2,000','$2,000 to $2,500','Over $3,000'],
                ],
                'move_in_date' => [
                    'type' => 'date', 'label' => 'When are you looking to move in?', 'required' => true,
                    'min_today' => true,
                ],
                'move_reason' => [
                    'type' => 'textarea', 'label' => 'Why are you moving?', 'required' => true, 'rows' => 4,
                ],
                'parking_importance' => [
                    'type' => 'select', 'label' => 'Is parking important?', 'required' => true,
                    'options' => ['Yes','No','Yes, but street parking is acceptable.','Yes, but it\'s not a deal breaker if it doesn\'t have one.'],
                ],
                'laundry_importance' => [
                    'type' => 'select', 'label' => 'Is laundry important?', 'required' => true,
                    'options' => ['Yes','No','Yes, but it\'s not a deal breaker if it doesn\'t have one.'],
                ],
                'preferred_floor' => [
                    'type' => 'select', 'label' => 'Do you have a preferred floor?', 'required' => true,
                    'options' => ['No Preference','1st Floor','2nd Floor','3rd Floor','3rd Floor or Higher','1st or 2nd Floor (No Higher Than 3rd Floor)'],
                ],
                'pet_type' => [
                    'type' => 'select', 'label' => 'Do you have any pets?', 'required' => true,
                    'options' => ['I don\'t have','Cat','Dog','Cat & Dog','Other'],
                ],
                'pet_type_other' => [
                    'type' => 'text', 'label' => 'What type of pet do you have?', 'required' => true,
                    'show_when' => 'data.pet_type === "Other"',
                ],
            ],
        ];
    }

    protected static function household(): array
    {
        return [
            'title' => 'Household Information',
            'fields' => [
                'people_count' => [
                    'type' => 'select', 'label' => 'How many people will be living in the apartment?', 'required' => true,
                    'options' => ['1','2','3','4','5','6','7','8','9','+10'],
                ],
                'adults_count' => [
                    'type' => 'select', 'label' => 'How many adults (18 or older)?', 'required' => true,
                    'options' => ['1','2','3','4','5','6','7','8','9','+10'],
                    'hide_when' => 'data.people_count === "1" || data.people_count === 1',
                ],
                'minors_count' => [
                    'type' => 'select', 'label' => 'How many minors (under 18)?', 'required' => true,
                    'options' => ['1','2','3','4','5','6','7','8','9','+10'],
                    'hide_when' => 'data.people_count === "1" || data.people_count === 1 || parseInt(data.adults_count) === parseInt(data.people_count)',
                ],
                'minors_ages' => [
                    'type' => 'text', 'label' => 'Please provide the ages of all minors under 18.', 'required' => true,
                    'help' => 'Separated by comma (e.g. 3, 7, 12)',
                    'show_when' => 'data.people_count !== "1" && data.people_count !== 1 && parseInt(data.adults_count) !== parseInt(data.people_count) && (data.minors_count && data.minors_count !== "")',
                ],
            ],
        ];
    }

    protected static function employment(): array
    {
        return [
            'title' => 'Employment & Income',
            'fields' => [
                'all_adults_employed' => [
                    'type' => 'select', 'label' => 'Are you and all other adults in the household currently employed?', 'required' => true,
                    'options' => ['Yes','No'],
                ],
                'employed_count' => [
                    'type' => 'select', 'label' => 'How many adults are currently employed?', 'required' => true,
                    'options' => ['1','2','3','4','5','6','7','8','9','+10'],
                    'show_when' => 'data.all_adults_employed === "No"',
                ],
                'monthly_income' => [
                    'type' => 'number', 'label' => 'What is the total monthly household income?', 'required' => true,
                    'prefix' => '$', 'min' => 0, 'step' => 50,
                ],
                'income_verification' => [
                    'type' => 'multiselect', 'label' => 'How can each working adult verify their income?', 'required' => true,
                    'options' => ['Direct Deposit','Paystubs','Company or Personal Check','Bank Statement','Cash','Other'],
                ],
                'income_other_explanation' => [
                    'type' => 'text', 'label' => 'Please explain:', 'required' => true,
                    'show_when' => 'Array.isArray(data.income_verification) && data.income_verification.includes("Other")',
                ],
            ],
        ];
    }

    protected static function background(): array
    {
        return [
            'title' => 'Credit, Rental History & Background Information',
            'fields' => [
                'credit_score' => [
                    'type' => 'select', 'label' => 'Please enter your credit score.', 'required' => true,
                    'options' => ['No Credit History','Below 550','550–599','600–649','650–699','700–749','750+'],
                ],
                'ssn_itin_status' => [
                    'type' => 'select', 'label' => 'Do you have a Social Security Number (SSN) or an Individual Taxpayer Identification Number (ITIN)?', 'required' => true,
                    'options' => ['Yes, I have an SSN','Yes, I have an ITIN','No, I have neither','I am an international student'],
                    'show_when' => 'data.credit_score === "No Credit History"',
                ],
                'can_provide_references' => [
                    'type' => 'select', 'label' => 'Can you provide landlord references?', 'required' => true,
                    'options' => ['Yes','No'],
                ],
                'references_explanation' => [
                    'type' => 'textarea', 'label' => 'If not, please explain why.', 'required' => true, 'rows' => 3,
                    'show_when' => 'data.can_provide_references === "No"',
                ],
                'had_eviction' => [
                    'type' => 'select', 'label' => 'Have you ever had an eviction?', 'required' => true,
                    'options' => ['Yes','No'],
                ],
                'eviction_explanation' => [
                    'type' => 'textarea', 'label' => 'If yes, please explain', 'required' => true, 'rows' => 3,
                    'show_when' => 'data.had_eviction === "Yes"',
                ],
                'housing_court_history' => [
                    'type' => 'select', 'label' => 'Do you have any Housing Court history?', 'required' => true,
                    'options' => ['Yes','No'],
                ],
                'housing_court_explanation' => [
                    'type' => 'textarea', 'label' => 'If yes, please explain', 'required' => true, 'rows' => 3,
                    'show_when' => 'data.housing_court_history === "Yes"',
                ],
                'outstanding_debts' => [
                    'type' => 'select', 'label' => 'Do you have any outstanding debts, accounts in collections, or unpaid bills?', 'required' => true,
                    'options' => ['Yes','No'],
                ],
                'outstanding_debts_explanation' => [
                    'type' => 'textarea', 'label' => 'If yes, please explain', 'required' => true, 'rows' => 3,
                    'show_when' => 'data.outstanding_debts === "Yes"',
                ],
                'felony_convictions' => [
                    'type' => 'select', 'label' => 'Do you have any felony convictions?', 'required' => true,
                    'options' => ['Yes','No'],
                ],
                'felony_explanation' => [
                    'type' => 'textarea', 'label' => 'If yes, please explain', 'required' => true, 'rows' => 3,
                    'show_when' => 'data.felony_convictions === "Yes"',
                ],
                'background_notes' => [
                    'type' => 'textarea', 'label' => 'Notes', 'rows' => 3,
                ],
            ],
        ];
    }

    protected static function voucher(): array
    {
        return [
            'title'    => 'Voucher Section',
            'subtitle' => 'A. Current Situation & Housing Needs',
            'fields' => [
                'voucher_bedrooms' => [
                    'type' => 'select', 'label' => 'How many bedrooms is your voucher approved for? (Voucher Size)', 'required' => true,
                    'options' => ['0 / Studio','1','2','3','4','+5'],
                ],
                'landlord_knows' => [
                    'type' => 'select', 'label' => 'Does your current landlord know that you are planning to move?', 'required' => true,
                    'options' => ['Yes','No'],
                ],
                'given_30day_notice' => [
                    'type' => 'select', 'label' => 'Have you already given your landlord a 30-day notice to vacate?', 'required' => true,
                    'options' => ['Yes','No'],
                ],
                'current_lease_expired' => [
                    'type' => 'select', 'label' => 'Has your current lease already expired?', 'required' => true,
                    'options' => ['Yes','No'],
                ],
                'voucher_move_reason' => [
                    'type' => 'textarea', 'label' => 'Why are you moving?', 'required' => true, 'rows' => 4,
                ],
                'voucher_household_size' => [
                    'type' => 'number', 'label' => 'How many people are included in your voucher household?', 'required' => true, 'min' => 1,
                ],
                'all_members_on_voucher' => [
                    'type' => 'select', 'label' => 'Are all household members listed on the voucher?', 'required' => true,
                    'options' => ['Yes','No'],
                ],
                'members_explanation' => [
                    'type' => 'textarea', 'label' => 'If no, please explain:', 'required' => true, 'rows' => 3,
                    'show_when' => 'data.all_members_on_voucher === "No"',
                ],
                'head_of_household_working' => [
                    'type' => 'select', 'label' => 'Is the head of household (voucher holder) currently working?', 'required' => true,
                    'options' => ['Yes','No'],
                ],
                'voucher_credit_score' => [
                    'type' => 'select', 'label' => 'Enter the credit score of the voucher head of household.', 'required' => true,
                    'options' => ['No Credit History','Below 550','550–599','600–649','650–699','700–749','750+'],
                ],
                'housing_authority' => [
                    'type' => 'select', 'label' => 'Which Housing Authority / Agency issued your voucher?', 'required' => true,
                    'options' => self::housingAuthorities(),
                ],
                'authority_other_explanation' => [
                    'type' => 'textarea', 'label' => 'If other, please specify:', 'required' => true, 'rows' => 3,
                    'show_when' => 'data.housing_authority === "Other Massachusetts Housing Authority"',
                ],
                'voucher_notes' => [
                    'type' => 'textarea', 'label' => 'Notes', 'rows' => 3,
                ],
            ],
        ];
    }

    protected static function showing(): array
    {
        return [
            'title' => 'Showing Availability',
            'fields' => [
                'available_days' => [
                    'type' => 'multiselect', 'label' => 'What days are you available for apartment showings? (Please select all that apply.)', 'required' => true,
                    'options' => ['Flexible / Any day', 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'],
                ],
                'available_time' => [
                    'type' => 'select', 'label' => 'What time are you available to view apartments?', 'required' => true,
                    'options' => ['Morning','Afternoon','Evening','I am flexible and available at any time'],
                ],
                'schedule_now' => [
                    'type' => 'select', 'label' => 'Would you like to schedule a showing now?', 'required' => true,
                    'options' => ['Yes','No'],
                ],
                'showing_date' => [
                    'type' => 'date', 'label' => 'Select the day of your showing:', 'required' => true,
                    'min_today' => true,
                    'show_when' => 'data.schedule_now === "Yes"',
                ],
                'showing_time' => [
                    'type' => 'select', 'label' => 'Select the time available for the showing:', 'required' => true,
                    'options' => ['8:00 AM','9:00 AM','10:00 AM','11:00 AM','12:00 PM','1:00 PM','2:00 PM','3:00 PM','4:00 PM','5:00 PM','6:00 PM','7:00 PM','8:00 PM'],
                    'show_when' => 'data.schedule_now === "Yes"',
                ],
            ],
        ];
    }


    protected static function voucherTypes(): array
    {
        return [
            'Section 8 (S8) Voucher / Housing Choice Voucher (HCV)',
            'MRVP — Massachusetts Rental Voucher Program',
            'Project-Based Voucher (PBV) / Project-Based Section 8',
            'Emergency Housing Voucher (EHV)',
            'Mainstream Voucher',
            'HUD-VASH (Veterans Affairs Supportive Housing)',
            'AHVP — Alternative Housing Voucher Program',
            'RAP — Rental Assistance Program',
            'DMH Rental Subsidy Program',
            'DDS Rental Assistance / Housing Support',
            'HomeBASE',
            'RAFT — Residential Assistance for Families in Transition',
            'Continuum of Care (CoC) Rental Assistance',
            'Rapid Re-Housing (RRH)',
            'ESG Rental Assistance (Emergency Solutions Grant)',
            'Local Housing Authority (LHA) Voucher',
            'Other',
        ];
    }

    protected static function housingAuthorities(): array
    {
        return [
            'Metro Housing Boston',
            'Boston Housing Authority (BHA)',
            'Cambridge Housing Authority (CHA)',
            'Chelsea Housing Authority',
            'Everett Housing Authority',
            'Lynn Housing Authority & Neighborhood Development (LHAND)',
            'Revere Housing Authority',
            'Somerville Housing Authority',
            'Malden Housing Authority',
            'Medford Housing Authority',
            'Quincy Housing Authority',
            'Waltham Housing Authority',
            'Newton Housing Authority',
            'Peabody Housing Authority',
            'Salem Housing Authority',
            'Framingham Housing Authority',
            'Brockton Housing Authority',
            'Lowell Housing Authority',
            'Lawrence Housing Authority',
            'Haverhill Housing Authority',
            'Worcester Housing Authority',
            'Springfield Housing Authority',
            'Holyoke Housing Authority',
            'Fall River Housing Authority',
            'New Bedford Housing Authority',
            'Taunton Housing Authority',
            'Plymouth Housing Authority',
            'Barnstable Housing Authority',
            'Other Massachusetts Housing Authority',
        ];
    }

    protected static function maCities(): array
    {
        return [
            'Boston','Cambridge','Somerville','Brookline','Newton','Quincy',
            'Chelsea','Everett','Revere','Malden','Medford','Arlington',
            'Waltham','Watertown','Belmont','Lexington','Woburn','Burlington',
            'Lynn','Salem','Peabody','Beverly','Danvers','Methuen','Lawrence',
            'Lowell','Haverhill','Framingham','Natick','Wellesley','Needham',
            'Dedham','Norwood','Brockton','Randolph','Braintree','Weymouth',
            'Worcester','Springfield','Holyoke','Fall River','New Bedford',
            'Other',
        ];
    }


    public static function isFieldVisible(string $key, array $data): bool
    {
        $hasVoucher = !empty($data['has_voucher']) && $data['has_voucher'] !== '0' && $data['has_voucher'] !== false;
        $data['has_voucher'] = $hasVoucher;

        $rules = [
            'voucher_type' => fn($d) => !empty($d['has_voucher']),
            'pet_type_other' => fn($d) => ($d['pet_type'] ?? '') === 'Other',

            'adults_count' => fn($d) => (string) ($d['people_count'] ?? '') !== '1',

            'minors_count' => function ($d) {
                if ((string) ($d['people_count'] ?? '') === '1') return false;
                $ac = (int) ($d['adults_count'] ?? 0);
                $pc = (int) ($d['people_count'] ?? 0);
                return !($ac > 0 && $ac === $pc);
            },

            'minors_ages' => function ($d) {
                if ((string) ($d['people_count'] ?? '') === '1') return false;
                $ac = (int) ($d['adults_count'] ?? 0);
                $pc = (int) ($d['people_count'] ?? 0);
                if ($ac > 0 && $ac === $pc) return false;
                return !empty($d['minors_count']);
            },

            'employed_count' => fn($d) => ($d['all_adults_employed'] ?? '') === 'No',

            'income_other_explanation' => fn($d) => is_array($d['income_verification'] ?? null)
                && in_array('Other', $d['income_verification'], true),

            'ssn_itin_status' => fn($d) => ($d['credit_score'] ?? '') === 'No Credit History',
            'references_explanation' => fn($d) => ($d['can_provide_references'] ?? '') === 'No',
            'eviction_explanation' => fn($d) => ($d['had_eviction'] ?? '') === 'Yes',
            'housing_court_explanation' => fn($d) => ($d['housing_court_history'] ?? '') === 'Yes',
            'outstanding_debts_explanation' => fn($d) => ($d['outstanding_debts'] ?? '') === 'Yes',
            'felony_explanation' => fn($d) => ($d['felony_convictions'] ?? '') === 'Yes',
            'members_explanation' => fn($d) => ($d['all_members_on_voucher'] ?? '') === 'No',
            'authority_other_explanation' => fn($d) => ($d['housing_authority'] ?? '') === 'Other Massachusetts Housing Authority',
            'showing_date' => fn($d) => ($d['schedule_now'] ?? '') === 'Yes',
            'showing_time' => fn($d) => ($d['schedule_now'] ?? '') === 'Yes',
        ];

        if (!isset($rules[$key])) return true;
        return $rules[$key]($data);
    }
}