<?php

namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(CartService $cartService): Response
    {
        $cartService->getTotal();
        return $this->render('cart/index.html.twig', [
            'cart' => $cartService->getTotal()
        ]);
    }

    #[Route('/cart/add/{id<\d+>}', name: 'app_cart_add')]
    public function add(CartService $cartService, int $id): Response
    {
        $cartService->addToCart($id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/delete/{id<\d+>}', name: 'app_cart_delete')]
    public function delete(CartService $cartService, int $id): Response
    {
        $cartService->removeFromCart($id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/decrement/{id<\d+>}', name: 'app_cart_decrement')]
    public function decrement(CartService $cartService, int $id): RedirectResponse
    {
        $cartService->decrementItemQuantity($id);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/clear', name: 'app_cart_clear')]
    public function clear(CartService $cartService): Response
    {
        $cartService->clearCart();

        return $this->redirectToRoute('app_products');
    }
}
