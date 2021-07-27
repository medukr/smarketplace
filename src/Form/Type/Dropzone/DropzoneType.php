<?php


namespace App\Form\Type\Dropzone;


use App\Repository\ImageRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DropzoneType extends AbstractType
{
    public function __construct(
        private ImageRepository $imageRepository
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'dropzone',
            CollectionType::class,
            [
                'entry_type' => HiddenType::class,
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => [
                    'class' => 'dropzone',
                    'id' => 'dropzone',
                    'action' => '/image/upload'
                ]
            ]
        )->addModelTransformer(new DropzoneTransformer($this->imageRepository));

        parent::buildForm($builder, $options);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'maxFiles' => 1,
            'addRemoveLinks' => true,
            'resizeWidth' => 0,
            'resizeHeight' => 0,
        ]);

        parent::configureOptions($resolver);
    }


    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var FormView $f */
        $f = $view->vars['form'];

        $view->vars['formName'] = $f->parent->vars['name'];
        $view->vars['id'] = $options['attr']['id'];
        $view->vars['maxFiles'] = $options['maxFiles'];
        $view->vars['img'] = [];
        $view->vars['addRemoveLinks'] = $options['addRemoveLinks'];
        $view->vars['resizeWidth'] = $options['resizeWidth'];
        $view->vars['resizeHeight'] = $options['resizeHeight'];
    }

}