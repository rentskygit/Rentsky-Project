<?php

namespace App\Services\Contracts\Product;

use Illuminate\Http\UploadedFile;

/**
 * Interface ImageStorageInterface
 * 
 * Define el contrato para el almacenamiento de imágenes
 * 
 * ¿Por qué esta interfaz? (Principio OCP - Abierto/Cerrado)
 * 
 * Hoy usamos almacenamiento LOCAL, pero mañana podríamos usar:
 * - Amazon S3
 * - Cloudinary
 * - Google Cloud Storage
 * - DigitalOcean Spaces
 * 
 * Con esta interfaz, podemos cambiar el sistema de almacenamiento
 * sin modificar el código del controlador o servicio.
 * 
 * Ejemplo de extensión (OCP):
 * - Crear CloudinaryStorageService implementando esta interfaz
 * - Cambiar el binding en el Service Provider
 * - ¡Todo sigue funcionando sin modificar una línea del controlador!
 */
interface ImageStorageInterface
{
    /**
     * Almacenar una imagen
     * 
     * @param UploadedFile $file Archivo de imagen
     * @param string $path Ruta donde guardar
     * @return string Ruta del archivo guardado
     */
    public function store(UploadedFile $file, string $path = ''): string;

    /**
     * Eliminar una imagen
     * 
     * @param string $path Ruta del archivo a eliminar
     * @return bool True si se eliminó correctamente
     */
    public function delete(string $path): bool;

    /**
     * Almacenar múltiples imágenes
     */
    public function storeMultiple(array $files, string $path = ''): array;

    /**
     * Eliminar múltiples imágenes
     */
    public function deleteMultiple(array $paths): bool;

    /**
     * Obtener la URL pública de una imagen
     */
    public function getPublicUrl(string $path): string;

    /**
     * Verificar si una imagen existe
     */
    public function exists(string $path): bool;
}