<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', methods: ['GET'], name: 'app_home')]
    public function index(): Response
    {
        $repository = $this->em->getRepository(Product::class);
        $games = $repository->findBy(['type' => 'game'], ['type' => 'ASC']);

        return $this->render('home/index.html.twig', [
            'games' => $games
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('contact/index.html.twig');
    }
}
