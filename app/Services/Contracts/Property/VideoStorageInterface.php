<?php
namespace App\Services\Contracts\Property;

use Illuminate\Http\UploadedFile;

interface VideoStorageInterface
{
    public function store(UploadedFile $file, string $path = ''): string;
    public function delete(string $path): bool;
    public function storeMultiple(array $files, string $path = ''): array;
    public function deleteMultiple(array $paths): bool;
    public function getPublicUrl(string $path): string;
    public function exists(string $path): bool;
}