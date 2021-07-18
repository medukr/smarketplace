<?php

namespace App\Entity;

use App\Repository\ImageToProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImageToProductRepository::class)
 */
class ImageToProduct
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\OneToOne(targetEntity=Image::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Image $image;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private Product $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

}
