<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Category;
use App\Entity\ImageToProduct;
use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\User;
use App\Form\AddAddressFormType;
use App\Form\AddShopFormType;
use App\Form\CategoryFormType;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProfileController extends AbstractController
{
    private const FLASH_INFO = 'info';

    #[Route('/user/profile', name: '_profile')]
    public function profile(): Response
    {
        return $this->render('profile/profile.html.twig', [
            'user' => $this->getUser()
        ]);
    }


    #[Route('/user/listShops', name: '_profile_list_shops')]
    public function listShops():Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('profile/listShops.html.twig', [
            'shops' => $user->getShops()
        ]);
    }


    #[Route('/user/viewShop/{id<\d+>}', name: '_profile_view_shop')]
    public function viewShop(Shop $shop, ProductRepository $productRepository, Request $request):Response
    {
        $page = (int) $request->get('page', 1);

        return $this->render('profile/viewShop.html.twig', [
            'user' => $this->getUser(),
            'shop' => $shop,
            'products' => $productRepository->getListByShop($shop, $page)
        ]);
    }


    #[Route('/user/editShop/{id<\d+>}', name: '_profile_edit_shop')]
    public function editShop(Request $request, EntityManagerInterface $em, TranslatorInterface $translator, Shop $shop):Response
    {
        $form = $this->createForm(AddShopFormType::class, $shop);
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($user instanceof User && $form->isSubmitted() && $form->isValid()) {
            $shop->addUser($user);
            $shop->setStatus($shop::STATUS_NEW);

            $em->persist($shop);
            $em->flush();

            $this->addFlash(self::FLASH_INFO, $translator->trans('shop.added'));

            return $this->redirectToRoute('_profile');
        }

        return $this->render('profile/form.html.twig', [
            'user' => $user,
            'Form' => $form->createView(),
            'contentTitle' => $translator->trans('shop.add')
        ]);
    }

    #[Route('/user/deleteShop/{id<\d+>}', name: '_profile_delete_shop')]
    public function deleteShop(Shop $shop, EntityManagerInterface $em):Response
    {
        $em->remove($shop);
        $em->flush();

        return $this->redirectToRoute('_profile');
    }


    #[Route('/user/addShop', name: '_profile_add_shop')]
    public function addShop(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $shop = new Shop();
        $form = $this->createForm(AddShopFormType::class, $shop);
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($user instanceof User && $form->isSubmitted() && $form->isValid()) {
            $shop->addUser($user);
            $shop->setStatus($shop::STATUS_NEW);

            $em->persist($shop);
            $em->flush();

            $this->addFlash(self::FLASH_INFO, $translator->trans('shop.added'));

            return $this->redirectToRoute('_profile');
        }

        return $this->render('profile/form.html.twig', [
            'user' => $user,
            'Form' => $form->createView(),
            'contentTitle' => $translator->trans('shop.add')
        ]);
    }

    #[Route('/user/addAddress', name: '_profile_add_address')]
    public function addAddress(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $address = new Address();
        $form = $this->createForm(AddAddressFormType::class, $address);
        $form->handleRequest($request);

        $user = $this->getUser();

        if ($user instanceof User && $form->isSubmitted() && $form->isValid()) {
            $address->addUser($user);

            $em->persist($address);
            $em->flush();

            $this->addFlash(self::FLASH_INFO, $translator->trans('address.added'));

            return $this->redirectToRoute('_profile');
        }

        return $this->render('profile/form.html.twig', [
            'Form' => $form->createView(),
            'contentTitle' => $translator->trans('address.add')
        ]);
    }


    #[Route('/user/addCategory', name: '_profile_add_category')]
    public function addCategory(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($category);
            $em->flush();

            $this->addFlash(self::FLASH_INFO, $translator->trans('category.added'));

            return $this->redirectToRoute('_profile');
        }

        return $this->render('profile/form.html.twig', [
            'Form' => $form->createView(),
            'contentTitle' => $translator->trans('category.add')
        ]);
    }


    #[Route('/user/addProduct/{shopId<\d+>}', name: '_profile_add_product')]
    public function addProduct(Request $request, EntityManagerInterface $em, TranslatorInterface $translator, int $shopId): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        /** @var Shop $shop */
        $shop = $em->find(Shop::class, $shopId);

        if ($shop instanceof Shop && $form->isSubmitted() && $form->isValid()) {
            $product->setShop($shop);

            foreach ($form['images']->getData() as $image) {
                $imageToProduct = (new ImageToProduct())
                    ->setImage($image)
                    ->setProduct($product);

                $em->persist($imageToProduct);

                $product->addImage($imageToProduct);
            }

            $em->persist($product);
            $em->flush();

            $this->addFlash(self::FLASH_INFO, $translator->trans('product.added'));

            return $this->redirectToRoute('_profile_view_shop', [
                'id' => $shopId
            ]);
        }

        return $this->render('profile/form.html.twig', [
            'Form' => $form->createView(),
            'contentTitle' => $translator->trans('product.add')
        ]);
    }
}
