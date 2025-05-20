<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    public function optimize($imagePath, $quality = 80, $maxWidth = 1200)
    {
        $image = $this->manager->read($imagePath);
        
        // Redimensionner l'image si elle est plus large que maxWidth
        if ($image->width() > $maxWidth) {
            $image->scale($maxWidth);
        }

        // Optimiser la qualité
        $image->toJpeg($quality);

        return $image;
    }

    public function save($image, $path)
    {
        return $image->save($path);
    }
} 