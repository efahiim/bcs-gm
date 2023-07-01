<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Review;
use App\Entity\Request;
use App\Entity\User;
use App\Entity\Message;
use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class DashboardController extends AbstractDashboardController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->chartBuilder = $chartBuilder;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $orders = $this->em->getRepository(Order::class)->findAll();
        $revenue = 0;

        foreach ($orders as $order) {
            if ($order->getStatus() === 'Paid') {
                $revenue += $order->getTotal();
            }
        }

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(ProductCrudController::class)->generateUrl());
        return $this->render('admin/index.html.twig', [
            'revenue' => $revenue,
            'chart' => $this->createChart()
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('GAMERMIND')
            ->setFaviconPath('/assets/images/favicon.png');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa-solid fa-home');
        yield MenuItem::linkToCrud('Products', 'fa-solid fa-gamepad', Product::class);
        yield MenuItem::linkToCrud('Reviews', 'fa-solid fa-comment', Review::class);
        yield MenuItem::linkToCrud('Requests', 'fa-solid fa-arrow-up-wide-short', Request::class);
        yield MenuItem::linkToCrud('Messages', 'fa-solid fa-message', Message::class);
        yield MenuItem::linkToCrud('Orders', 'fa-solid fa-cart-shopping', Order::class);
        yield MenuItem::linkToCrud('Users', 'fa-solid fa-users', User::class)->setPermission('ROLE_ADMIN');
    }

    private function createChart(): Chart
    {
        // 
    }
}
