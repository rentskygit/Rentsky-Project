<?php

return [
    'enabled' => true,

    'bot_name' => 'JCM Assistant',
    'welcome_message' => 'Hi! 👋 I\'m here to help you with apartments, requirements, and the rental process. What can I help you with?',

    'quick_replies_welcome' => [
        'Show available apartments',
        'How do I apply?',
        'Pet policy',
        'Requirements to qualify',
        'Contact info',
    ],

    'fallback_message' => 'I\'m not sure about that yet. You can browse our apartments or reach out to our team directly — we reply within 24 hours.',

    'fallback_actions' => [
        ['label' => 'Browse apartments', 'url' => '/apartments'],
        ['label' => 'Contact team', 'url' => 'mailto:agonzalez@jcmrealtygroup.com'],
    ],

    /*
    |------------------------------------------------------------------
    | KNOWLEDGE BASE
    |------------------------------------------------------------------
    | Cada entrada:
    | - keywords: array de palabras clave (se hace match case-insensitive
    |   y por contención).
    | - answer: respuesta. Puede contener HTML simple.
    | - quick_replies: sugerencias siguientes (opcional).
    | - actions: botones con URL (opcional).
    |
    | NO incluir aquí información sensible (usuarios, applications
    | privadas, datos personales). Solo info pública de la web.
    */
    'entries' => [
        [
            'id' => 'greeting',
            'keywords' => ['hi', 'hello', 'hey', 'hola', 'good morning', 'good afternoon', 'good evening'],
            'answer' => 'Hello! I can help you with:<br>• Finding an apartment<br>• The qualification and application process<br>• Requirements and pet policies<br>• Contact information<br><br>What would you like to know?',
            'quick_replies' => ['Show available apartments', 'How do I apply?', 'Pet policy', 'Contact info'],
        ],
        [
            'id' => 'apartments_list',
            'keywords' => ['apartments', 'apartment', 'listing', 'listings', 'available', 'properties', 'browse', 'show me apartments', 'what apartments'],
            'answer' => 'Browse all our available apartments at our dedicated page. You can filter by category, price, and location, and view each one on the map.',
            'actions' => [
                ['label' => 'Browse apartments', 'url' => '/apartments'],
            ],
            'quick_replies' => ['What are the requirements?', 'Pet policy', 'How do I apply?'],
        ],
        [
            'id' => 'apartments_count',
            'keywords' => ['how many apartments', 'how many properties', 'total apartments', 'how many listings'],
            'answer' => '__APARTMENT_COUNT__',
            'actions' => [
                ['label' => 'See all apartments', 'url' => '/apartments'],
            ],
        ],
        [
            'id' => 'price_range',
            'keywords' => ['price range', 'how much', 'rent cost', 'average rent', 'starting price', 'cheapest', 'most expensive'],
            'answer' => '__PRICE_RANGE__',
            'actions' => [
                ['label' => 'See listings', 'url' => '/apartments'],
            ],
        ],
        [
            'id' => 'how_to_apply',
            'keywords' => ['how do i apply', 'apply', 'application', 'application process', 'how to apply', 'submit application'],
            'answer' => 'Here\'s how the rental process works:<br><br><strong>1. Browse</strong> our available apartments.<br><strong>2. Complete a qualification</strong> — a quick form to confirm your budget and rental history.<br><strong>3. Submit an application</strong> for the property you\'re interested in.<br><strong>4. Get approved</strong> — our team reviews your file and reaches out to schedule a showing.<br><br>Everything is done online, and your progress is saved as you go.',
            'actions' => [
                ['label' => 'Start qualification', 'url' => '/qualifications'],
                ['label' => 'Start application', 'url' => '/applications'],
            ],
            'quick_replies' => ['What are the requirements?', 'How long does it take?'],
        ],
        [
            'id' => 'qualification_info',
            'keywords' => ['qualification', 'qualify', 'what is qualification', 'qualification form'],
            'answer' => 'A <strong>qualification</strong> is a short form we use to understand your housing needs, budget, and rental history. It usually takes 5–10 minutes to complete. Once submitted, it helps us match you with the right apartments and speeds up the application process.',
            'actions' => [
                ['label' => 'Go to qualification', 'url' => '/qualifications'],
            ],
        ],
        [
            'id' => 'application_info',
            'keywords' => ['application', 'what is application', 'rental application', 'application form'],
            'answer' => 'The <strong>rental application</strong> is a detailed form that covers your contact info, residential history, employment, income, references, and any co-applicants or pets. You can save your progress and come back anytime.',
            'actions' => [
                ['label' => 'Go to application', 'url' => '/applications'],
            ],
        ],
        [
            'id' => 'how_long_apply',
            'keywords' => ['how long', 'how many days', 'approval time', 'wait time', 'when approved', 'response time'],
            'answer' => 'Once you submit your application, our team reviews it within <strong>24 hours</strong> and reaches out to schedule a showing if it\'s a good fit. The full process — from application to move-in — typically takes 3–7 days depending on the property and availability.',
            'quick_replies' => ['How do I apply?', 'What are the requirements?'],
        ],
        [
            'id' => 'requirements',
            'keywords' => ['requirements', 'qualify', 'credit score', 'income required', 'minimum income', 'credit check', 'screening'],
            'answer' => 'To qualify, applicants typically need:<br><br>• <strong>Combined gross monthly income</strong> of at least 3× the monthly rent<br>• <strong>Credit and background screening</strong> for all adult applicants<br>• <strong>Positive rental history</strong> (landlord references preferred)<br>• <strong>No recent evictions</strong> (case-by-case for older ones)<br><br>All criteria are applied consistently to every applicant, in compliance with fair housing laws.',
            'quick_replies' => ['How do I apply?', 'Pet policy', 'Contact info'],
        ],
        [
            'id' => 'income_requirement',
            'keywords' => ['income requirement', 'how much income', 'salary required', 'how much do i need to make', '3x rent', '3 times rent'],
            'answer' => 'Most of our properties require a <strong>combined gross monthly income of at least 3× the monthly rent</strong>. For example, a $2,500/month apartment would require at least $7,500/month in household income. Each listing page shows its specific requirement.',
        ],
        [
            'id' => 'pet_policy',
            'keywords' => ['pet', 'pets', 'pet policy', 'dog', 'dogs', 'cat', 'cats', 'pet friendly', 'pet-friendly', 'mascota'],
            'answer' => 'Pet policies vary by property — most of our listings are <strong>pet-friendly</strong> with some restrictions.<br><br>• Cats: usually allowed<br>• Small dogs: allowed with weight limits (often up to 25 lbs)<br>• Large dogs: property-specific<br>• Maximum number of pets: property-specific<br><br>Each apartment listing shows its exact pet policy, including allowed pets, weight limits, and pet fees if applicable.',
            'quick_replies' => ['Show available apartments', 'Requirements to qualify'],
        ],
        [
            'id' => 'eviction_policy',
            'keywords' => ['eviction', 'evicted', 'eviction history', 'past eviction'],
            'answer' => 'Evictions are reviewed case-by-case. If you have an eviction on record, we consider the age and circumstances. Evictions <strong>more than 5 years old</strong> typically carry less weight. Each property listing shows its specific screening criteria.',
            'quick_replies' => ['Requirements to qualify', 'Contact info'],
        ],
        [
            'id' => 'deposit',
            'keywords' => ['deposit', 'security deposit', 'how much deposit', 'move in cost', 'move-in cost'],
            'answer' => 'Security deposits vary by property and are listed on each apartment\'s detail page. Typically deposits are <strong>equal to one month\'s rent</strong>, sometimes with additional pet deposits if applicable. The exact amount is always shown before you apply.',
        ],
        [
            'id' => 'utilities',
            'keywords' => ['utilities', 'water', 'electricity', 'gas', 'internet', 'trash', 'who pays utilities', 'included'],
            'answer' => 'Utilities vary by property. Each apartment listing shows exactly which utilities are included (if any) — such as water, trash, electricity, gas, or internet. Any utilities not listed as included are the tenant\'s responsibility.',
        ],
        [
            'id' => 'parking',
            'keywords' => ['parking', 'parking spot', 'garage', 'car', 'parking included', 'carport'],
            'answer' => 'Parking availability varies by property. Options include:<br>• Garage (attached or detached)<br>• Carport<br>• Off-street parking<br>• Street parking (on-street)<br><br>Each listing specifies the parking type and, if applicable, the assigned parking spot number.',
        ],
        [
            'id' => 'contact',
            'keywords' => ['contact', 'phone', 'email', 'reach out', 'talk to someone', 'contact you', 'get in touch', 'human'],
            'answer' => 'You can reach our team at:<br><br>📧 <strong>agonzalez@jcmrealtygroup.com</strong><br>📞 Response within 24 hours on business days.<br><br>For apartment-specific questions, browse to the property and use the "Schedule a showing" button.',
            'actions' => [
                ['label' => 'Send email', 'url' => 'mailto:agonzalez@jcmrealtygroup.com'],
            ],
        ],
        [
            'id' => 'create_account',
            'keywords' => ['create account', 'sign up', 'register', 'new account', 'how do i sign up'],
            'answer' => 'You can create a free account to track your qualification and application, save drafts, and get updates. Click "Sign in" at the top, then "Register" to create your account.',
            'actions' => [
                ['label' => 'Create account', 'url' => '/register'],
            ],
        ],
        [
            'id' => 'login',
            'keywords' => ['login', 'sign in', 'log in', 'how do i log in', 'forgot password'],
            'answer' => 'You can sign in from the top-right of any page. If you forgot your password, use the "Forgot password" link on the login page.',
            'actions' => [
                ['label' => 'Sign in', 'url' => '/login'],
            ],
        ],
        [
            'id' => 'profile',
            'keywords' => ['my profile', 'profile', 'my account', 'change password', 'update email'],
            'answer' => 'Once signed in, click on your avatar at the top-right and select "My profile" to update your name, email, or password.',
        ],
        [
            'id' => 'about',
            'keywords' => ['about', 'who are you', 'what is jcm', 'jcm realty', 'about jcm', 'company'],
            'answer' => 'JCM Realty Group is a Boston-based rental agency helping tenants find quality apartments across Massachusetts. We focus on transparent listings, honest timelines, and responsive support throughout the rental process.',
        ],
        [
            'id' => 'locations',
            'keywords' => ['location', 'locations', 'where', 'boston', 'massachusetts', 'area', 'neighborhood', 'neighborhoods'],
            'answer' => 'We serve Boston and the surrounding Massachusetts area — including neighborhoods like Chelsea, Lynn, Cambridge, Revere, and others. Browse the apartments page to see properties on the map by location.',
            'actions' => [
                ['label' => 'View on map', 'url' => '/apartments'],
            ],
        ],
        [
            'id' => 'virtual_tour',
            'keywords' => ['virtual tour', 'video tour', 'see video', 'walkthrough', 'photos'],
            'answer' => 'Each apartment listing includes photos, and many have a virtual tour or video walkthrough. Open the apartment detail page to view all photos and videos.',
            'actions' => [
                ['label' => 'Browse apartments', 'url' => '/apartments'],
            ],
        ],
        [
            'id' => 'availability',
            'keywords' => ['available now', 'when available', 'move in date', 'move-in date', 'availability', 'available date'],
            'answer' => 'Availability is shown on each apartment listing — either as "Available now" or with a specific move-in date. If you have a specific date in mind, mention it when scheduling a showing.',
        ],
        [
            'id' => 'showings',
            'keywords' => ['schedule showing', 'showing', 'tour', 'visit', 'see in person', 'appointment'],
            'answer' => 'To schedule a showing, open the apartment you\'re interested in and click "Schedule a showing". Showings are usually available within 1–3 business days of your request.',
        ],
        [
            'id' => 'thanks',
            'keywords' => ['thank', 'thanks', 'thank you', 'cool', 'great', 'awesome', 'perfect'],
            'answer' => 'You\'re welcome! If you have any other questions, just ask. 😊',
        ],
    ],

    /*
    |------------------------------------------------------------------
    | OPENAI UPGRADE (opcional)
    |------------------------------------------------------------------
    | Si quieres reemplazar la lógica rule-based por OpenAI, configura:
    |
    | 'openai_key' => env('OPENAI_API_KEY'),
    | 'openai_model' => 'gpt-4o-mini',
    |
    | Y en ChatbotController, en lugar de `respond()` usar
    | `respondWithOpenAI()`. El sistema de knowledge base sigue activo
    | como contexto del prompt.
    */
    'openai_key'         => env('OPENAI_API_KEY'),
    'openai_model'       => env('OPENAI_MODEL', 'gpt-4o-mini'),
    'openai_enabled'     => env('OPENAI_ENABLED', true),
    'openai_timeout'     => env('OPENAI_TIMEOUT', 20),
    'openai_max_tokens'  => env('OPENAI_MAX_TOKENS', 500),
    'openai_temperature' => env('OPENAI_TEMPERATURE', 0.4),
];