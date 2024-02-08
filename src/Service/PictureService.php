<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PictureService
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function add(UploadedFile $picture, ?string $folder = '', ?int $width = 350, ?int $height = 350) :string
    {
        // on donne un nouveau nom à l'image
        $file = md5(uniqid(rand(), true)) . '.jpeg';

        // on récupère les infos de l'image
        $pictureInfos = getimagesize($picture);

        if ($pictureInfos === false) {
            throw new Exception('Format d\'image incorrect');
        }

        // on vérifie le format de l'image
        switch ($pictureInfos['mime']) {
            case 'image/png':
                $pictureSource = imagecreatefrompng($picture);
                break;
            
            case 'image/jpeg':
                $pictureSource = imagecreatefromjpeg($picture);
                break;

            case 'image/webp':
                $pictureSource = imagecreatefromwebp($picture);
                break;

            default:
                throw new Exception('Format d\'image incorrect');
        }

        // on recadre l'image
        // on récupère les dimension 
        $imageWidth = $pictureInfos[0];
        $imageHeight = $pictureInfos[1];

        // on vérifie l'orientation de l'image

        switch ($imageWidth <=> $imageHeight) {
            case -1: // portrait
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = ($imageHeight - $squareSize) / 2;
                break;
            
            case 0: // carré
                $squareSize = $imageWidth;
                $src_x = 0;
                $src_y = 0;
                break;
            

            case 1: // paysage
                $squareSize = $imageHeight;
                $src_x = ($imageWidth - $squareSize) / 2;
                $src_y = 0;
                break;
        }

        // on crée une nouvelle image "vierge"
        $resizedPicture = imagecreatetruecolor($width,$height);

        imagecopyresampled($resizedPicture, $pictureSource, 0, 0, $src_x, $src_y, $width, $height, $squareSize, $squareSize);

        $path = $this->params->get('images_directory') . $folder;

        // on crée le dossier de destination
        if (!file_exists($path . '/mini/')) {
            mkdir($path . '/mini/', 0755, true);
        }


        // on stocke l'image recadée
        imagejpeg($resizedPicture, $path . '/mini/' . $width . 'x' . $height . '-' . $file);

        $picture->move($path . '/' . $file);

        $miniFile = $width . 'x' . $height . '-' . $file;

        return $miniFile;
    }

    public function delete(string $file, ?string $folder = '', ?int $width = 250, ?int $height = 250) :bool
    {
        if($file !== 'default.webp'){
            $success = false;
            $path = $this->params->get('images_directory') . $folder;

            $mini = $path . '/mini/' . $width . 'x' . $height . '-' . $file;

            if (file_exists($mini)) {
                unlink($mini);
                $success = true;
            }

            $original = $path . $file;

            if (file_exists($original)) {
                unlink($original);
                $success = true;
            }
            
            return $success;
        }

        return false;
    }
}