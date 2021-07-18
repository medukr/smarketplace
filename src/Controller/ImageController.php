<?php

namespace App\Controller;

use App\Entity\Image;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    #[Route('/image/upload', name: 'image_upload')]
    public function upload(Request $request, EntityManagerInterface $em, array $imgConfig): Response
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('file');

        if ($file instanceof UploadedFile) {
            $fileName = uniqid('img', true) . '.' . $file->guessExtension();

            $originalName = $file->getClientOriginalName();
            $size = $file->getSize();

            $fileUpload = $file->move($imgConfig['path'], $fileName);

            $url = $imgConfig['url'] . $fileUpload->getFilename();
            $path = $fileUpload->getRealPath();
            $mime = $fileUpload->getMimeType();

            $img = new Image();
            $img->setName($fileName)
                ->setMime($mime)
                ->setSize($size)
                ->setPath($path)
                ->setUrl($url);

            $em->persist($img);
            $em->flush();

            return $this->json([
                'success' => true,
                'id' => $img->getId()
            ]);
        }

        return $this->json([
            'success' => false
        ]);
    }

    #[Route('/image/load/{id<\d+>}', name: 'image_load', methods: ['GET'])]
    public function load(Image $image): Response
    {
        return $this->json([
            'success' => true,
            'data' => [
                'id' => $image->getId(),
                'name' => $image->getName(),
                'url' => $image->getUrl(),
                'size' => $image->getSize(),
                'type' => $image->getMime()
            ]
        ]);
    }


    #[Route('/image/delete/{id<\d+>}', name: 'image_delete', methods: ['GET'])]
    public function delete(Image $image, EntityManagerInterface $em, Filesystem $filesystem): Response
    {
        $path = $image->getPath();

        $em->remove($image);
        $em->flush();

        $filesystem->remove($path);

        return $this->json([
            'success' => true
        ]);
    }
}
