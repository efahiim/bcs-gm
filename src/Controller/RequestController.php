<?php

namespace App\Controller;

use App\Entity\Request as RequestEntity;
use App\Form\RequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class RequestController extends AbstractController
{
    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    #[Route('/requests', name: 'app_requests')]
    public function index(Request $request): Response
    {
        $user = $this->security->getUser();
        $userRequests = $user->getRequests();

        $newRequest = new RequestEntity();
        $newRequest->setStatus('Pending');
        $newRequest->setUser($user);
        $form = $this->createForm(RequestFormType::class, $newRequest);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newRequest = $form->getData();
            
            $this->em->persist($newRequest);
            $this->em->flush();

            return $this->redirect($request->getUri());
        }

        return $this->render('request/index.html.twig', [
            'requests' => $userRequests,
            'form' => $form->createView()
        ]);
    }
}
