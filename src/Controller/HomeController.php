<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    private $productRepository;
    private $CategoryRepository;
    private $entityManager;
    public function __construct(ProductRepository $productRepository, ManagerRegistry $doctrine, CategoryRepository $CategoryRepository)
    {
        $this->productRepository = $productRepository;
        $this->CategoryRepository = $CategoryRepository;
        $this->entityManager = $doctrine->getManager();
    }
    #[Route('/', name: 'home')]
    public function index(): Response
    {

        $products = $this->productRepository->findAll();
        $categories = $this->CategoryRepository->findAll();

        return $this->render('home/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
    #[Route('/produc/{category}', name: 'product_category')]
    public function categoryProducts(Category $category): Response
    {
        $categories = $this->CategoryRepository->findAll();

        return $this->render('home/index.html.twig', [
            'products' => $category->getProducts(),
            'categories' => $categories,
        ]);
    }
}
