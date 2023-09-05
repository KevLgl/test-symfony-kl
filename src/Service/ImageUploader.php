<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Gd\Imagine;

class ImageUploader
{
    private $uploadsDirectory;
    private $imagine;

    public function __construct(ParameterBagInterface $parameterBag, Imagine $imagine)
    {
        $this->uploadsDirectory = $parameterBag->get('uploads_directory');
        $this->imagine = $imagine;
    }

    public function upload(UploadedFile $file): string
    {
        $fileName = uniqid().'.'.$file->guessExtension();
        $file->move($this->uploadsDirectory, $fileName);
        $this->resizeImage($fileName, 50, 50);

        return $fileName;
    }

    public function resizeImage(string $fileName, int $width, int $height): void
    {
        $filePath = $this->uploadsDirectory . '/' . $fileName;
        $image = $this->imagine->open($filePath);
        $image = $this->resize($image, $width, $height);
        $image->save($filePath);
    }

    private function resize(ImageInterface $image, int $width, int $height): ImageInterface
    {
        $imageSize = $image->getSize();
        $aspectRatio = $imageSize->getWidth() / $imageSize->getHeight();

        // Calculate new dimensions while preserving aspect ratio
        if ($width === null) {
            $width = round($height * $aspectRatio);
        } elseif ($height === null) {
            $height = round($width / $aspectRatio);
        }

        $newSize = new Box($width, $height);
        return $image->resize($newSize);
    }
}
