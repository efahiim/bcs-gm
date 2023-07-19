<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Address;
use App\Form\AddressFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        $repository = $this->em->getRepository(Product::class);
        $games = $repository->findBy(['type' => 'game'], ['type' => 'ASC']);

        return $this->render('home/index.html.twig', [
            'games' => $games
        ]);
    }

    #[Route('/account/{username}', name: 'app_account')]
    public function account(string $username, Request $request): Response
    {
        $user = $this->security->getUser();
        $currentAddress = $user->getAddress();
        
        if ($currentAddress != null) {
            $form = $this->createForm(AddressFormType::class, $currentAddress);
        } else {
            $currentAddress = new Address();
            $currentAddress->setUser($user);
            $form = $this->createForm(AddressFormType::class, $currentAddress);
        }
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $editedAddress = $form->getData();

            $this->em->persist($editedAddress);
            $this->em->flush();

            return $this->redirect($request->getUri());
        }

        return $this->render('security/account.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
