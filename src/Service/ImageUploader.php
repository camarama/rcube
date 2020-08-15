<?php


namespace App\Service;



use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class ImageUploader
{
    private $targetDirectory;

    /**
     *
     * @param $targetDirectory
     */
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $uploadedFile)
    {
        $originalImagename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeImagename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalImagename);
        $imageName = $safeImagename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

        try
        {
            $uploadedFile->move(
                $this->getTargetDirectory(),
                $imageName
            );
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