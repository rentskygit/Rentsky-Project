<?php

namespace App\Console\Commands;

use App\Models\Property\Property;
use App\Services\GeocodingService;
use Illuminate\Console\Command;

class RegeneratePropertyCoordinates extends Command
{
    protected $signature = 'properties:geocode
                            {--force : Re-geocodifica incluso las que ya tienen coordenadas}';

    protected $description = 'Genera lat/lng para propiedades sin coordenadas (o todas con --force)';

    public function handle(GeocodingService $geocoder): int
    {
        $query = Property::query();

        if (!$this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('latitude')->orWhereNull('longitude');
            });
        }

        $properties = $query->get();

        if ($properties->isEmpty()) {
            $this->info('No hay propiedades por geocodificar.');
            return self::SUCCESS;
        }

        $this->info("Procesando {$properties->count()} propiedades...");
        $bar = $this->output->createProgressBar($properties->count());

        $success = 0;
        $failed  = 0;

        foreach ($properties as $p) {
            $coords = $geocoder->geocode($p->address, $p->city, $p->state, $p->zip_code);

            if ($coords) {
                $p->update([
                    'latitude'  => $coords['lat'],
                    'longitude' => $coords['lng'],
                ]);
                $success++;
            } else {
                $failed++;
                $bar->clear();
                $this->warn("  ✗ No geocodificada: {$p->address}, {$p->city}, {$p->state} {$p->zip_code}");
                $bar->display();
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ {$success} geocodificadas correctamente.");

        if ($failed > 0) {
            $this->warn("✗ {$failed} no se pudieron geocodificar.");
        }

        return self::SUCCESS;
    }
}