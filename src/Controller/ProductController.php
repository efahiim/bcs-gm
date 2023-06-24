<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Review;
use App\Form\ReviewFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class ProductController extends AbstractController
{
    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
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

    #[Route('/products/{id}', name: 'app_product')]
    public function product(int $id, Request $request): Response
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

        if (count($ratings) > 0) {
            $productRating = $productRating / count($ratings);
        } else $productRating = 5;

        // Adding a new review
        $user = $this->security->getUser();
        $review = new Review();
        $review->setProduct($product);
        $review->setUser($user);
        $form = $this->createForm(ReviewFormType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newReview = $form->getData();
            $this->em->persist($newReview);
            $this->em->flush();

            return $this->redirect($request->getUri());
        }

        return $this->render('products/show.html.twig', [
            'product' => $product,
            'reviews' => $reviews,
            'productRating' => $productRating,
            'form' => $form->createView()
        ]);
    }
}
