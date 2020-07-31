<?php


namespace App\Service;


use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader
{
    private $targetDirectory;
    /**
     * @var UploadedFile
     */
    private $image;

    /**
     *
     * @param $targetDirectory
     */
    public function __construct($targetDirectory, UploadedFile $image)
    {
        $this->targetDirectory = $targetDirectory;
        $this->image = $image;
    }

    public function upload()
    {
        $originalImagename = pathinfo($this->image->getClientOriginalName(), PATHINFO_FILENAME);
//        $safeImagename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalImagename);
        $imageName = Urlizer::urlize($originalImagename).'-'.uniqid().'.'.$this->image->guessExtension();

        try
        {
            $this->image->move($this->getTargetDirectory(), $imageName);
        } catch (FileException $e){
            // Gérer l'exeption ultérieument ici
        }

        return $imageName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}