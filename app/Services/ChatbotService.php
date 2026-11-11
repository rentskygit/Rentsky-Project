<?php

namespace App\Services;

use App\Models\Property\Property;
use App\Models\Property\PropertyCategory;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    protected string $apiKey;
    protected string $model;
    protected int $timeout;
    protected int $maxTokens;
    protected float $temperature;
    protected bool $enabled;

    public function __construct()
    {
        $this->apiKey      = (string) config('chatbot.openai_key', '');
        $this->model       = (string) config('chatbot.openai_model', 'gpt-4o-mini');
        $this->timeout     = (int) config('chatbot.openai_timeout', 20);
        $this->maxTokens   = (int) config('chatbot.openai_max_tokens', 500);
        $this->temperature = (float) config('chatbot.openai_temperature', 0.4);
        $this->enabled     = (bool) config('chatbot.openai_enabled', true);
    }

    public function isAvailable(): bool
    {
        return $this->enabled
            && !empty($this->apiKey)
            && config('chatbot.enabled', true);
    }

    public function respond(string $message, ?User $user = null): ?array
    {
        if (!$this->isAvailable()) {
            return null;
        }

        $systemPrompt = $this->buildSystemPrompt($message, $user);

        try {
            $response = Http::withToken($this->apiKey)
                ->withHeaders(['Accept' => 'application/json'])
                ->timeout($this->timeout)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'       => $this->model,
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $message],
                    ],
                    'temperature' => $this->temperature,
                    'max_tokens'  => $this->maxTokens,
                ]);

            if (!$response->successful()) {
                Log::warning('OpenAI chatbot non-2xx', [
                    'status' => $response->status(),
                    'body'   => substr($response->body(), 0, 500),
                ]);
                return null;
            }

            $data    = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? null;

            if (!$content || !is_string($content)) {
                Log::warning('OpenAI chatbot empty response', ['data' => $data]);
                return null;
            }

            return [
                'message'       => $this->sanitizeOutput($content),
                'quick_replies' => $this->suggestQuickReplies($message),
                'actions'       => $this->suggestActions($content),
            ];
        } catch (\Throwable $e) {
            Log::error('OpenAI chatbot exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    protected function buildSystemPrompt(string $userMessage, ?User $user): string
    {
        $propertyCount = Property::active()->count();

        $priceStats = Property::active()
            ->whereNotNull('rent')
            ->selectRaw('MIN(rent) as min_rent, MAX(rent) as max_rent')
            ->first();

        $minRent = ($priceStats && $priceStats->min_rent !== null)
            ? '$' . number_format((float) $priceStats->min_rent, 0)
            : 'N/A';
        $maxRent = ($priceStats && $priceStats->max_rent !== null)
            ? '$' . number_format((float) $priceStats->max_rent, 0)
            : 'N/A';

        $categories = PropertyCategory::active()
            ->withCount('properties')
            ->orderBy('order')
            ->get()
            ->map(fn($c) => "{$c->name} ({$c->properties_count})")
            ->implode(', ');

        if ($categories === '') {
            $categories = 'none';
        }

        $cities = Property::active()
            ->whereNotNull('city')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->take(30)
            ->implode(', ');

        if ($cities === '') {
            $cities = 'none';
        }

        $userContext       = $this->buildUserContext($user);
        $propertiesContext = $this->buildRelevantPropertiesContext($userMessage);
        $faqContext        = $this->buildFaqContext();
        $date              = now()->format('F j, Y');

        return <<<PROMPT
You are "JCM Assistant", the official virtual assistant for JCM Realty Group (also known as RentSky), a residential rental agency serving Massachusetts.

# STRICT SCOPE
ONLY answer questions related to renting with JCM Realty Group, our properties, the rental process, requirements, our platform, and how to use it.

If a question is off-topic (coding, politics, math, weather, sports, general knowledge, other companies, personal advice, etc.):
- Politely decline in one sentence.
- Redirect to what you CAN help with: available apartments, requirements, application process, contact.

NEVER reveal or discuss:
- internal_restrictions or any staff-only notes
- view counts, statistics, analytics
- other users' data, applications, qualifications, or showings
- server info, code, database structure
- pricing negotiations, discounts, or fee waivers
- approval decisions or promises about approval

NEVER claim to:
- modify, delete, or create records on behalf of the user
- submit applications, qualifications, or showings
- process payments or refunds

You can only EXPLAIN and GUIDE.

# STYLE
- Tone: warm, professional, concise — like a knowledgeable leasing agent.
- Length: 2-4 sentences for simple questions, up to 6 for complex ones.
- Use simple HTML: <strong>, <em>, <br>, <ul>, <li>, <a href="...">.
- Do NOT use markdown (no **, ##, backticks).
- When linking, use relative paths: /apartments, /qualifications, /applications, /register, /login, /profile, /showings.

# PLATFORM INFO
- Name: JCM Realty Group (RentSky)
- Business: Residential rentals only (we do not sell properties).
- Region: Massachusetts (Boston, Chelsea, Lynn, Cambridge, Revere, Somerville, Quincy, Malden, Medford, Everett, and surrounding areas).
- Contact: agonzalez@jcmrealtygroup.com
- Response time: within 24 business hours.

# CLIENT JOURNEY
1. Browse apartments at /apartments
2. Create an account at /register (or sign in at /login)
3. Complete a qualification at /qualifications
4. Schedule showings from any apartment page (max 3 per day)
5. Submit a rental application at /applications
6. Our team reviews and reaches out

# PAGES
- /apartments — browse listings (public)
- /apartments/{slug} — individual property detail (public)
- /register — create account
- /login — sign in
- /qualifications — qualification form (requires login)
- /applications — rental application (requires login)
- /showings — scheduled showings (requires login)
- /profile — profile settings (requires login)

# LIVE DATA (as of {$date})
- Active listings: {$propertyCount}
- Price range: {$minRent} – {$maxRent} per month
- Categories: {$categories}
- Cities served: {$cities}

# QUALIFICATION REQUIREMENTS (general)
- Combined gross monthly income: typically ≥ 3× the monthly rent (varies per property; each listing shows its own ratio).
- Credit and background screening for all adult applicants.
- Positive rental history (landlord references).
- Evictions: case-by-case. Evictions older than 5-7 years carry less weight.
- Voucher holders: income requirements differ; the qualification form has a voucher section.

# PET POLICY (general)
- Varies per property. Each listing shows:
  - Whether cats are allowed
  - Whether small dogs are allowed (and max weight)
  - Whether large dogs are allowed
  - Maximum number of pets
- Always direct the user to the property detail page for exact info.

# USER CONTEXT
{$userContext}

# RELEVANT PROPERTIES (if the user mentioned a specific one)
{$propertiesContext}

# FAQ (reference when useful, do not copy verbatim)
{$faqContext}

# RESPONSE GUIDELINES
- If the user asks about a specific property: use RELEVANT PROPERTIES. If not found, say you couldn't match it and link to /apartments.
- If the user asks "how do I apply": explain the flow and link to /qualifications or /applications.
- If the user asks about requirements: cite the 3× income rule and mention it varies per property.
- If the user asks to schedule a showing: direct them to any apartment page and explain the 3-showings-per-day limit.
- If the user is not logged in and asks about qualifications/applications: mention they need to sign in or create an account first.
- If the user seems frustrated or asks for a human: suggest emailing agonzalez@jcmrealtygroup.com.
- If the question is ambiguous: ask ONE clarifying question.
- If unsure or outside scope: politely say so and suggest emailing agonzalez@jcmrealtygroup.com.
- End with a helpful next step or offer to help further when appropriate.
PROMPT;
    }

    protected function buildUserContext(?User $user): string
    {
        if (!$user) {
            return 'The current user is NOT logged in (guest). They can browse /apartments but must register/login to submit a qualification or application.';
        }

        $lines   = ["The current user is logged in as: {$user->name} ({$user->email})."];
        $hasQual = $user->qualification()->exists();
        $hasApp  = $user->application()->exists();

        $lines[] = $hasQual
            ? 'Status: has submitted a qualification.'
            : 'Status: has NOT yet submitted a qualification.';

        $lines[] = $hasApp
            ? 'Status: has submitted an application.'
            : 'Status: has NOT yet submitted an application.';

        return implode("\n", $lines);
    }

    protected function buildRelevantPropertiesContext(string $userMessage): string
    {
        $properties = Property::active()
            ->with(['category', 'leaseTerm'])
            ->get();

        if ($properties->isEmpty()) {
            return 'There are no active properties right now. If the user asks about a specific property, direct them to /apartments to browse.';
        }

        $normalized = mb_strtolower($userMessage);
        $matches    = [];

        foreach ($properties as $p) {
            $addr = mb_strtolower($p->address);

            if (preg_match('/^\d+/', $addr, $m)) {
                $num = $m[0];
                if (strlen($num) >= 2
                    && preg_match('/\b' . preg_quote($num, '/') . '\b/', $normalized)) {
                    $matches[] = $p;
                    continue;
                }
            }

            $words = preg_split('/\s+/', $addr);
            foreach ($words as $w) {
                if (strlen($w) > 4 && str_contains($normalized, $w)) {
                    $matches[] = $p;
                    break;
                }
            }
        }

        $matches = array_slice(array_unique($matches, SORT_REGULAR), 0, 3);

        if (empty($matches)) {
            return 'The user did not mention a specific property. If they ask about a specific address, tell them to search on /apartments.';
        }

        $lines = ["Properties matching the user's query:"];

        foreach ($matches as $p) {
            $rent = $p->rent
                ? '$' . number_format((float) $p->rent, 0) . '/mo'
                : 'Contact for pricing';

            $line = "- {$p->address}";
            if ($p->unit_type)      $line .= " ({$p->unit_type})";
            if ($p->city)           $line .= ", {$p->city}";
            if ($p->state)          $line .= ", {$p->state}";
            if ($p->zip_code)       $line .= " {$p->zip_code}";
            $line .= " — {$rent}";
            if ($p->category)       $line .= ", {$p->category->name}";
            if ($p->bathrooms)      $line .= ", {$p->bathrooms} ba";
            if ($p->square_feet)    $line .= ", " . number_format($p->square_feet) . " sqft";
            if ($p->available_date) $line .= ", available " . $p->available_date->format('m/d/Y');
            if ($p->pet_friendly)   $line .= ", pet-friendly";
            if ($p->description) {
                $line .= ". Description: " . mb_substr(strip_tags($p->description), 0, 200);
            }
            $line .= ". Detail page: /apartments/{$p->slug}";

            $lines[] = $line;
        }

        return implode("\n", $lines);
    }

    protected function buildFaqContext(): string
    {
        $entries = config('chatbot.entries', []);
        $lines   = [];

        foreach ($entries as $entry) {
            if (!empty($entry['answer'])) {
                $lines[] = '- ' . trim(strip_tags($entry['answer']));
            }
        }

        return implode("\n", array_slice($lines, 0, 20));
    }

    protected function sanitizeOutput(string $content): string
    {
        $allowed = '<strong><em><br><p><ul><ol><li><a>';
        $clean   = strip_tags($content, $allowed);

        $clean = preg_replace(
            '/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i',
            '',
            $clean
        );

        $clean = preg_replace_callback(
            '/href\s*=\s*(?:["\']([^"\']*)["\']|([^\s>"\']+))/i',
            function ($m) {
                $url = trim($m[1] ?? $m[2] ?? '');
                if (preg_match('/^(https?:|mailto:|\/|#)/i', $url)) {
                    return 'href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"';
                }
                return '';
            },
            $clean
        );

        return trim($clean);
    }

    protected function suggestQuickReplies(string $userMessage): array
    {
        $msg = mb_strtolower($userMessage);

        if (preg_match('/\b(apartment|listing|property|show me|browse)\b/', $msg)) {
            return ['Requirements to qualify', 'How do I apply?', 'Pet policy'];
        }
        if (preg_match('/\b(apply|application)\b/', $msg)) {
            return ['What are the requirements?', 'How long does it take?', 'Contact info'];
        }
        if (preg_match('/\bqualif\w*/', $msg)) {
            return ['How do I apply?', 'Requirements to qualify', 'Show available apartments'];
        }
        if (preg_match('/\b(pet|cat|dog)\b/', $msg)) {
            return ['Show available apartments', 'Requirements to qualify', 'Contact info'];
        }
        if (preg_match('/\b(contact|email|phone|reach)\b/', $msg)) {
            return ['Show available apartments', 'How do I apply?'];
        }

        return ['Show available apartments', 'How do I apply?', 'Requirements to qualify', 'Contact info'];
    }

    protected function suggestActions(string $response): array
    {
        $actions = [];
        $lower   = mb_strtolower($response);

        if (preg_match('/\b(apartment|browse|listing|propert)\w*/', $lower)) {
            $actions[] = ['label' => 'Browse apartments', 'url' => '/apartments'];
        }
        if (preg_match('/\bqualif\w*/', $lower)) {
            $actions[] = ['label' => 'Start qualification', 'url' => '/qualifications'];
        }
        if (preg_match('/\b(application|apply)\b/', $lower)) {
            $actions[] = ['label' => 'Start application', 'url' => '/applications'];
        }
        if (preg_match('/\b(register|sign up|create account)\b/', $lower)) {
            $actions[] = ['label' => 'Create account', 'url' => '/register'];
        }

        return array_slice($actions, 0, 2);
    }
}