<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    public function geocode(
        string $address,
        ?string $city = null,
        ?string $state = null,
        ?string $zip = null
    ): ?array {
        if (empty($address) && empty($city)) {
            return null;
        }

        $coords = $this->query([
            'street'      => $address,
            'city'        => $city,
            'state'       => $state,
            'postalcode'  => $zip,
            'country'     => 'United States',
        ]);

        if ($coords) {
            return $coords;
        }

        $freeForm = trim(implode(', ', array_filter([$address, $city, $state, $zip, 'USA'])));
        $coords = $this->query(['q' => $freeForm]);

        if ($coords) {
            return $coords;
        }

        if ($city && $state) {
            $coords = $this->query([
                'city'    => $city,
                'state'   => $state,
                'country' => 'United States',
            ]);

            if ($coords) {
                Log::info('Geocoding aproximado (solo ciudad)', [
                    'original_address' => $address,
                    'city'             => $city,
                    'state'            => $state,
                ]);
                return $coords;
            }
        }

        Log::warning('Geocoding sin resultados', [
            'address' => $address,
            'city'    => $city,
            'state'   => $state,
            'zip'     => $zip,
        ]);

        return null;
    }

    protected function query(array $params): ?array
    {
        $defaults = [
            'format'         => 'json',
            'limit'          => 1,
            'addressdetails' => 1,
            'countrycodes'   => 'us',  
        ];

        if (!isset($params['q'])) {

        }

        $attempts = 0;

        while ($attempts < 2) {
            $attempts++;

            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'RentSky/1.0 (contact@rentsky.app)',
                    'Accept'     => 'application/json',
                ])
                ->withOptions(['verify' => false])
                ->timeout(15)
                ->get('https://nominatim.openstreetmap.org/search', array_merge($defaults, $params));

                if ($response->status() === 429) {
                    sleep(2);
                    continue;
                }

                if (!$response->successful()) {
                    Log::warning('Nominatim error HTTP', [
                        'status' => $response->status(),
                        'params' => $params,
                    ]);
                    return null;
                }

                $data = $response->json();

                if (empty($data[0]['lat']) || empty($data[0]['lon'])) {
                    return null;
                }

                usleep(1_100_000); 

                return [
                    'lat' => (float) $data[0]['lat'],
                    'lng' => (float) $data[0]['lon'],
                ];
            } catch (\Throwable $e) {
                Log::error('Geocoding exception', [
                    'params' => $params,
                    'error'  => $e->getMessage(),
                ]);
                sleep(1);
            }
        }

        return null;
    }
}