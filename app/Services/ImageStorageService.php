<?php

namespace App\Services;

use App\Services\Contracts\Product\ImageStorageInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class ImageStorageService
 * 
 * SERVICIO DE ALMACENAMIENTO DE IMÁGENES
 * 
 * ¿Qué hace?
 * - Guarda imágenes en el sistema de archivos (disco público)
 * - Optimiza imágenes (redimensiona, comprime)
 * - Maneja la eliminación de imágenes
 * 
 * PRINCIPIOS APLICADOS:
 * 
 * 1. SRP: Solo maneja operaciones de almacenamiento de imágenes
 * 2. OCP: Podemos cambiar el sistema de almacenamiento (S3, Cloudinary)
 *    implementando ImageStorageInterface
 * 3. LSP: Cualquier implementación puede sustituir a esta
 * 
 * EJEMPLO DE EXTENSIÓN (OCP):
 * 
 * Para cambiar a Amazon S3:
 * 1. Crear S3StorageService implementando ImageStorageInterface
 * 2. Modificar el binding en RepositoryServiceProvider
 * 3. ¡El código existente sigue funcionando sin modificaciones!
 */
class ImageStorageService implements ImageStorageInterface
{
    protected $disk;
    protected $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function __construct()
    {
        $this->disk = Storage::disk('public');
    }

    /**
     * Almacenar una imagen
     * 
     * Validaciones:
     * - Tipo de archivo permitido
     * - Generación de nombre único
     * - Optimización automática
     */
    public function store(UploadedFile $file, string $path = ''): string
    {
        // 1. Validar extensión
        $extension = $file->getClientOriginalExtension();
        if (!in_array(strtolower($extension), $this->allowedExtensions)) {
            throw new \InvalidArgumentException('Tipo de archivo no permitido. Solo: ' . implode(', ', $this->allowedExtensions));
        }

        // 2. Generar nombre único
        $filename = Str::random(40) . '.' . $extension;
        $fullPath = $path ? $path . '/' . $filename : $filename;

        // 3. Guardar el archivo
        $this->disk->put($fullPath, file_get_contents($file));

        // 4. Optimizar imagen (opcional)
        $this->optimizeImage($fullPath);

        return $fullPath;
    }

    /**
     * Eliminar una imagen
     */
    public function delete(string $path): bool
    {
        if ($this->exists($path)) {
            return $this->disk->delete($path);
        }
        return false;
    }

    /**
     * Almacenar múltiples imágenes
     */
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

    /**
     * Eliminar múltiples imágenes
     */
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

    /**
     * Obtener URL pública de una imagen
     */
    public function getPublicUrl(string $path): string
    {
        return asset('storage/' . $path);
    }

    /**
     * Verificar si una imagen existe
     */
    public function exists(string $path): bool
    {
        return $this->disk->exists($path);
    }

    /**
     * Optimizar una imagen
     * 
     * Método privado para mantener SRP
     * Puedes implementar optimización con Intervention Image u otra librería
     */
    private function optimizeImage(string $path): void
    {
        // EJEMPLO CON INTERVENTION IMAGE (requiere composer require intervention/image)
        /*
        use Intervention\Image\Facades\Image;
        
        $imagePath = $this->disk->path($path);
        $image = Image::make($imagePath);
        
        // Redimensionar manteniendo aspecto
        $image->resize(800, 800, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        
        // Comprimir
        $image->save(null, 80);
        */
        
        // Por ahora, mantenemos la imagen sin procesar
        // En producción, descomenta el código de arriba
    }
}