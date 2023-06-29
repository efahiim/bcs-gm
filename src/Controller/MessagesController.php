<?php

namespace App\Controller;
use App\Entity\Chat;
use App\Entity\Message;
use App\Form\MessageFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class MessagesController extends AbstractController
{
    private $em;
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    #[Route('/messages', name: 'app_messages')]
    public function index(Request $request): Response
    {
        $user = $this->security->getUser();
        $userChat = $user->getChat();
        $messages = $user->getMessages();

        if ($userChat == null) {
            $userChat = new Chat();
            $userChat->setCustomer($user);
            $this->em->persist($userChat);
            $this->em->flush();
        };

        $newMessage = new Message();
        $newMessage
            ->setChat($userChat)
            ->setUser($user);

        $form = $this->createForm(MessageFormType::class, $newMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formMessage = $form->getData();
            
            $this->em->persist($formMessage);
            $this->em->flush();

            return $this->redirect($request->getUri());
        }

        return $this->render('messages/index.html.twig', [
            'messages' => $messages,
            'form' => $form->createView()
        ]);
    }
}
