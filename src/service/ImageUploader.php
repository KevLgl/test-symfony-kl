<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageUploader
{
    private $uploadsDirectory;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->uploadsDirectory = $parameterBag->get('uploads_directory');
    }

    public function upload(UploadedFile $file): string
    {
        $fileName = uniqid().'.'.$file->guessExtension();
        $file->move($this->uploadsDirectory, $fileName);

        return $fileName;
    }
}

