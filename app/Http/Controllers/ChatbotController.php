<?php

namespace App\Http\Controllers;

use App\Models\Property\Property;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function message(Request $request, ChatbotService $chatbot)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = trim($request->input('message'));

        if (empty($message)) {
            return response()->json([
                'message' => config('chatbot.welcome_message'),
                'quick_replies' => config('chatbot.quick_replies_welcome'),
                'actions' => [],
            ]);
        }

        $aiResponse = $chatbot->respond($message, auth()->user());
        if ($aiResponse !== null) {
            return response()->json($aiResponse);
        }

        $entry = $this->findEntry($message);

        if (!$entry) {
            return response()->json([
                'message' => config('chatbot.fallback_message'),
                'quick_replies' => [],
                'actions' => config('chatbot.fallback_actions'),
            ]);
        }

        $answer = $this->resolveDynamicPlaceholders($entry['answer']);

        return response()->json([
            'message' => $answer,
            'quick_replies' => $entry['quick_replies'] ?? [],
            'actions' => $entry['actions'] ?? [],
        ]);
    }

    protected function findEntry(string $message): ?array
    {
        $normalized = $this->normalize($message);
        $entries    = config('chatbot.entries', []);

        $bestMatch = null;
        $bestScore = 0;

        foreach ($entries as $entry) {
            $score = 0;

            foreach ($entry['keywords'] as $keyword) {
                $kw = $this->normalize($keyword);

                if ($kw === '') {
                    continue;
                }

                if ($normalized === $kw) {
                    $score += 100;
                } elseif (str_contains($normalized, $kw)) {
                    $score += 10 + strlen($kw);
                } elseif ($this->allWordsMatch($normalized, $kw)) {
                    $score += 5;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $entry;
            }
        }

        return $bestScore > 0 ? $bestMatch : null;
    }

    protected function allWordsMatch(string $message, string $keyword): bool
    {
        $words = preg_split('/\s+/', $keyword);
        if (count($words) < 2) {
            return false;
        }

        foreach ($words as $word) {
            if (!str_contains($message, $word)) {
                return false;
            }
        }

        return true;
    }

    protected function normalize(string $text): string
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    protected function resolveDynamicPlaceholders(string $answer): string
    {
        if (str_contains($answer, '__APARTMENT_COUNT__')) {
            $count = Property::active()->count();

            if ($count === 0) {
                return 'We don\'t have any apartments published right now — check back soon, or contact our team for upcoming listings.';
            }

            return 'We currently have <strong>' . $count . ' ' . ($count === 1 ? 'apartment' : 'apartments') . '</strong> available. Browse them all and filter by category, price, or location.';
        }

        if (str_contains($answer, '__PRICE_RANGE__')) {
            $stats = Property::active()
                ->whereNotNull('rent')
                ->selectRaw('MIN(rent) as min_rent, MAX(rent) as max_rent, COUNT(*) as total')
                ->first();

            if (!$stats || !$stats->total) {
                return 'Pricing is shown on each apartment listing. Browse our available apartments to see current prices.';
            }

            $min = '$' . number_format((float) $stats->min_rent, 0);
            $max = '$' . number_format((float) $stats->max_rent, 0);

            if ((float) $stats->min_rent === (float) $stats->max_rent) {
                return 'Our current listings are priced at <strong>' . $min . '/month</strong>. Browse them all to see details.';
            }

            return 'Our current apartments range from <strong>' . $min . '</strong> to <strong>' . $max . ' per month</strong>. Browse all listings to filter by your budget.';
        }

        return $answer;
    }
}