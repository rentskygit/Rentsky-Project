<?php

namespace App\Services;

use App\Services\Contracts\Property\PropertyImageStorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyImageStorageService implements PropertyImageStorageInterface
{
    protected $disk;
    protected array $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }

    public function store(UploadedFile $file, string $path = ''): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $this->allowedExtensions)) {
            throw new \InvalidArgumentException('File type not allowed. Only: ' . implode(', ', $this->allowedExtensions));
        }

        $filename = Str::random(40) . '.' . $extension;
        $fullPath = $path ? $path . '/' . $filename : $filename;

        $this->disk->put($fullPath, file_get_contents($file));

        return $fullPath;
    }

    public function delete(string $path): bool
    {
        if ($this->exists($path)) {
            return $this->disk->delete($path);
        }
        return false;
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
        $success = true;
        foreach ($paths as $path) {
            if (!$this->delete($path)) {
                $success = false;
            }
        }
        return $success;
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