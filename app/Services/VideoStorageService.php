<?php
namespace App\Services;

use App\Services\Contracts\Property\VideoStorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoStorageService implements VideoStorageInterface
{
    protected $disk;
    protected array $allowedExtensions = ['mp4','webm','mov'];

    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }

    public function store(UploadedFile $file, string $path = ''): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $this->allowedExtensions, true)) {
            throw new \InvalidArgumentException('Tipo de video no permitido. Solo: ' . implode(', ', $this->allowedExtensions));
        }

        $filename = Str::random(40) . '.' . $ext;
        $fullPath = $path ? $path . '/' . $filename : $filename;

        // Para videos grandes usamos stream en lugar de file_get_contents
        $stream = fopen($file->getRealPath(), 'r');
        $this->disk->put($fullPath, $stream);
        if (is_resource($stream)) fclose($stream);

        return $fullPath;
    }

    public function delete(string $path): bool
    {
        return $this->exists($path) && $this->disk->delete($path);
    }

    public function storeMultiple(array $files, string $path = ''): array
    {
        $paths = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = $this->store($file, $path);
            }
        }
        return $paths;
    }

    public function deleteMultiple(array $paths): bool
    {
        $ok = true;
        foreach ($paths as $p) { if (!$this->delete($p)) $ok = false; }
        return $ok;
    }

    public function getPublicUrl(string $path): string
    {
        return asset('storage/' . $path);
    }

    public function exists(string $path): bool
    {
        return $this->disk->exists($path);
    }
}