<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;

class OrderController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/order', name: 'app_order')]
    public function index(CartService $cartService, Request $request): Response
    {
        $user = $this->getUser();
        $userAddress = $user->getAddress();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if(!$user->getAddress()) {
            return $this->redirectToRoute('app_account', [
                'username' => $user->getUsername()
            ]);
        }

        if (count($cartService->getTotal()) < 1) {
            return $this->redirectToRoute('app_products');
        }

        $form = $this->createForm(OrderFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $deliveryAddress = $userAddress->getZipCode() . ', ' . $userAddress->getStreet() . ', ' . $userAddress->getCity() . ', ' . $userAddress->getCountry();

            $order = new Order();
            $order->setUser($user);
            $order->setReference(uniqid());
            $order->setDeliveryAddress($deliveryAddress);
            $order->setStatus('Cart');

            $this->em->persist($order);

            foreach ($cartService->getTotal() as $item) {
                $orderDetails = new OrderDetails();
                $orderDetails->setOrderProduct($order);
                $orderDetails->setQuantity($item['quantity']);
                $orderDetails->setPrice($item['product']->getPrice());
                $orderDetails->setProduct($item['product']->getTitle());
                $orderDetails->setTotalPrice($item['product']->getPrice() * $item['quantity']);

                $this->em->persist($orderDetails);
            }

            $this->em->flush();

            return $this->redirectToRoute('app_checkout');
        }

        return $this->render('order/index.html.twig', [
            'cart' => $cartService->getTotal(),
            'address' => $userAddress,
            'form' => $form->createView()
        ]);
    }

    #[Route('/checkout', name: 'app_checkout')]
    public function checkout(Request $request): Response
    {
        $user = $this->getUser();
        $userAddress = $user->getAddress();

        $form = $this->createForm(OrderFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
        }

        return $this->render('order/checkout.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
