<?php


namespace App\Form\Type\Dropzone;


use App\Repository\ImageRepository;
use Symfony\Component\Form\DataTransformerInterface;

class DropzoneTransformer implements DataTransformerInterface
{

    public function __construct(
        private ImageRepository $imageRepository
    ) {}

    public function transform($value)
    {
//        dump($value);
    }

    public function reverseTransform($value)
    {
        return $this->imageRepository->findBy(['id' => $value['dropzone']]);
    }
}