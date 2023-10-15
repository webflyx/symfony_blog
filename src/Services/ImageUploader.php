<?php

namespace App\Services;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageUploader
{
    private $targetDirectory;
    private $sluger;

    public function __construct($targetDirectory, SluggerInterface $sluger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->sluger = $sluger;
    }

    public function upload(UploadedFile $file)
    {
        $originalFileName = pathinfo(
            $file->getClientOriginalName(),
            PATHINFO_FILENAME
        );
        $safeFileName = $this->sluger->slug($originalFileName);

        
        $newFileName = $safeFileName . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move(
                $this->getTargetDirectory('profiles_directory'),
                $newFileName
            );
        } catch (FileException $e) {
        }    

        return $newFileName;
    }

    public function getTargetDirectory() 
    {
        return $this->targetDirectory;
    }
}