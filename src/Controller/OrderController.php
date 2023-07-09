<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class OrderController extends AbstractController
{
    private EntityManagerInterface $em;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator)
    {
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
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

        $deliveryAddress = $userAddress->getZipCode() . ', ' . $userAddress->getStreet() . ', ' . $userAddress->getCity() . ', ' . $userAddress->getCountry();
        $total = 0;

        foreach ($cartService->getTotal() as $item) {
            $itemTotal = $item['product']->getPrice() * $item['quantity'];
            $total += $itemTotal;
        }

        $order = new Order();
        $order->setUser($user);
        $order->setReference(uniqid());
        $order->setDeliveryAddress($deliveryAddress);
        $order->setStatus('Cart');
        $order->setTotal($total);

        $this->em->persist($order);

        $form = $this->createForm(OrderFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

            return $this->redirectToRoute('app_payment', [
                'reference' => $order->getReference()
            ]);
        }

        return $this->render('order/index.html.twig', [
            'cart' => $cartService->getTotal(),
            'address' => $userAddress,
            'form' => $form->createView()
        ]);
    }

    public function getPaypalClient(): PayPalHttpClient
    {
        $clientId = 'ARjJ6CVymMw9lFXjvh4vBcP1CNyLmVT0u1o3sUzB40KQMPuYemVTHFIufreRzRqP2x7R9xWgwa8nCXBu';
        $clientSecret = 'ELHDQywJOST3nfE1mQexI7vk6EODBZDj12HR2yCMo0wfGAdjrgJT0nLESLSn-tAFDv-Cd4e4vY-l-NrC';
        $environment = new SandboxEnvironment($clientId, $clientSecret);

        return new PayPalHttpClient($environment);
    }

    #[Route('/payment/{reference}', name: 'app_payment')]
    public function createPaypalSession(string $reference): RedirectResponse
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);

        if (!$order) {
            return $this->redirectToRoute('app_cart');
        }

        $items = [];
        $itemTotal = 0;

        foreach ($order->getOrderDetails()->getValues() as $product) {
            $items[] = [
                'name' => $product->getProduct(),
                'quantity' => $product->getQuantity(),
                'unit_amount' => [
                    'value' => $product->getPrice() / 100,
                    'currency_code' => 'USD'
                ]
            ];

            $itemTotal += ($product->getPrice() / 100) * $product->getQuantity();
        }

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => $itemTotal,
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => 'USD',
                                'value' => $itemTotal,
                            ],
                            'shipping' => [
                                'currency_code' => 'USD',
                                'value' => 0,
                            ]
                        ]
                    ],
                    'items' => $items
                ]
            ],
            'application_context' => [
                'return_url' => $this->urlGenerator->generate('payment_success', ['reference' => $order->getReference()], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->urlGenerator->generate('payment_failed', ['reference' => $order->getReference()], UrlGeneratorInterface::ABSOLUTE_URL)
            ]
        ];

        $client = $this->getPaypalClient();
        $response = $client->execute($request);

        if ($response->statusCode != 201) {
            return $this->redirectToRoute('app_order');
        }

        $approvalLink = '';

        foreach ($response->result->links as $link) {
            if ($link->rel === 'approve') {
                $approvalLink = $link->href;
                break;
            }
        }

        if (empty($approvalLink)) {
            return $this->redirectToRoute('app_order');
        }
        

        $order->setPaypalId($response->result->id);

        $this->em->flush();

        return new RedirectResponse($approvalLink);
    }

    #[Route('/payment/success/{reference}', name: 'payment_success')]
    public function paymentSuccess(string $reference, CartService $cartService)
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);

        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_cart');
        }

        $order->setStatus('Paid');
        $cartService->clearCart();

        $this->em->flush();

        return $this->render('order/success.html.twig', [
            'order' => $order
        ]);
    }

    #[Route('/payment/failed/{reference}', name: 'payment_failed')]
    public function paymentFailed(string $reference)
    {
        $order = $this->em->getRepository(Order::class)->findOneBy(['reference' => $reference]);

        if (!$order || $order->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('app_cart');
        }

        return $this->render('order/failed.html.twig', [
            'order' => $order
        ]);
    }

    #[Route('/orders', name: 'app_orders')]
    public function userOrders()
    {
        $user = $this->getUser();
        $userOrders = $user->getOrders();

        return $this->render('order/orders.html.twig', [
            'orders' => $userOrders
        ]);
    }
}
