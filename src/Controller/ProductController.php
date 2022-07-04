<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private $productRepository;
    private $entityManager;
    public function __construct(ProductRepository $productRepository, ManagerRegistry $doctrine)
    {
        $this->productRepository = $productRepository;
        $this->entityManager = $doctrine->getManager();
    }


    #[Route('/product', name: 'product_list')]
    public function index(): Response
    {
        $product  = $this->productRepository->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $product,
        ]);
    }
    #[Route('/produc/details/{id}', name: 'product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/produc/edit/{id}', name: 'product_edit')]
    public function editProduct(Product $product, Request $request): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            if ($request->files->get('product')['image']) {
                $image = $request->files->get('product')['image'];
                $image_name = time() . '_' . $image->getClientOriginalName();
                // Move the file to the directory where brochures are stored
                $image->move(
                    $this->getParameter('image_directory'),
                    $image_name
                );

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $product->setImage($image_name);
            }
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'Your product was update'
            );
            return $this->redirectToRoute('product_list');
        }
        return $this->renderForm('product/edit.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/produc/delete/{id}', name: 'product_delete')]
    public function delete(Product $product): Response
    {

        $filesystem = new Filesystem();
        $imagePath = './uploads/images/' . $product->getImage();
        if ($filesystem->exists($imagePath)) {
            $filesystem->remove($imagePath);
        }
        $this->entityManager->remove($product);
        $this->entityManager->flush();
        $this->addFlash(
            'success',
            'Your product was removed'
        );
        return $this->redirectToRoute('product_list');
    }

    #[Route('/store/product', name: 'product_store')]
    public function store(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product = $form->getData();
            if ($request->files->get('product')['image']) {
                $image = $request->files->get('product')['image'];
                $image_name = time() . '_' . $image->getClientOriginalName();
                // Move the file to the directory where brochures are stored
                $image->move(
                    $this->getParameter('image_directory'),
                    $image_name
                );

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $product->setImage($image_name);
            }
            $this->entityManager->persist($product);
            $this->entityManager->flush();
            $this->addFlash(
                'success',
                'Your product was saved'
            );
            return $this->redirectToRoute('product_list');
        }
        return $this->renderForm('product/create.html.twig', [
            'form' => $form
        ]);
    }
}
