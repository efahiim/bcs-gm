<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/products', methods: ['GET'], name: 'app_products')]
    public function products(): Response
    {
        $repository = $this->em->getRepository(Product::class);
        $games = $repository->findBy(['type' => 'game'], ['type' => 'ASC']);
        $devices = $repository->findBy(['type' => 'device'], ['type' => 'ASC']);

        return $this->render('products/index.html.twig', [
            'games' => $games,
            'devices' => $devices
        ]);
    }

    #[Route('/products/{id}', methods: ['GET'], name: 'app_product')]
    public function product($id): Response
    {
        $productRepository = $this->em->getRepository(Product::class);
        $product = $productRepository->find($id);
        $reviews = $product->getReviews();
        $ratings = [];
        $productRating = 0;

        foreach ($reviews as $review) {
            $reviewRating = $review->getRating();
            array_push($ratings, $reviewRating);
        }

        foreach ($ratings as $rating) {
            $productRating = $productRating + $rating;
        }

        $productRating = $productRating / count($ratings);

        return $this->render('products/show.html.twig', [
            'product' => $product,
            'reviews' => $reviews,
            'productRating' => $productRating
        ]);
    }
}
