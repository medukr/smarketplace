<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\Type\Dropzone\DropzoneType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProductFormType
 * @package App\Form
 */
class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('images', DropzoneType::class, [
                'label' => 'image.add',
                'attr' => [
                    // Для загрузки изображений нужен уникальный id
                    'id' => 'imgProduct'
                ],
                'maxFiles' => 3,
                'mapped' => false,

            ])
            ->add('name', TextType::class, [
                'label' => 'product.name'
            ])
            ->add('description', TextType::class, [
                'label' => 'product.description'
            ])
            ->add('category', EntityType::class, [
                'label' => 'product.category',
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'product.submit'
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver):void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
