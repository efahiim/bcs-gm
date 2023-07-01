<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Review;
use App\Entity\Message;
use App\Entity\Product;
use App\Entity\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\BarChart;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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

        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(ProductCrudController::class)->generateUrl());
        return $this->render('admin/index.html.twig', [
            'revenue' => $revenue,
            'gamesChart' => $this->createGamesChart(),
            'devicesChart' => $this->createDevicesChart()
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

    private function createGamesChart(): BarChart
    {
        $games = $this->em->getRepository(Product::class)->findBy(['type' => 'game'], ['type' => 'ASC']);
        $gamesChartData = [
            ['Game', 'Reviews', 'Rating', 'Orders', 'Revenue']
        ];

        foreach ($games as $game) {
            $gameReviews = $game->getReviews();
            $gameReviewsCount = count($gameReviews) > 0 ? count($gameReviews) : 0;
            $gameReviewsRatings = [];
            $gameAverageRating = 0;

            $gameOrders = $this->em->getRepository(OrderDetails::class)->findBy(['product' => $game->getTitle()], ['product' => 'ASC']);
            $gameOrdersTotal = 0;
            $gameOrdersRevenue = 0;

            if (count($gameOrders) > 0) {
                foreach ($gameOrders as $gameOrder) {
                    $gameOrdersTotal += $gameOrder->getQuantity();
                    $gameOrdersRevenue += $gameOrder->getTotalPrice();
                }
            }

            foreach ($gameReviews as $review) {
                $reviewRating = $review->getRating();
                array_push($gameReviewsRatings, $reviewRating);
            }

            foreach ($gameReviewsRatings as $rating) {
                $gameAverageRating =+ $rating;
            }

            if (count($gameReviewsRatings) > 0) {
                $gameAverageRating += count($gameReviewsRatings);
            } else $gameAverageRating = 5;

            array_push($gamesChartData, [$game->getTitle(), $gameReviewsCount, $gameAverageRating, $gameOrdersTotal, $gameOrdersRevenue]);
        }

        $chart = new BarChart();
        $chart->getData()->setArrayToDataTable($gamesChartData);
        $chart->getOptions()
            ->setTitle('Games')
            ->setWidth(900)
            ->setHeight(500)
            ->setSeries([['axis' => 'Reviews'], ['axis' => 'Rating'], ['axis' => 'Orders'], ['axis' => 'Revenue']])
            ->setAxes(['x' => [
                'Reviews' => ['side' => 'top', 'label' => 'Reviews'],
                'Rating' => ['side' => 'top', 'label' => 'Rating'],
                'Orders' => ['side' => 'bottom', 'label' => 'Orders'],
                'Revenue' => ['side' => 'bottom', 'label' => 'Revenue']
            ]]);

        return $chart;
    }

    private function createDevicesChart(): BarChart
    {
        $devices = $this->em->getRepository(Product::class)->findBy(['type' => 'device'], ['type' => 'ASC']);
        $devicesChartData = [
            ['Device', 'Reviews', 'Rating', 'Orders', 'Revenue']
        ];

        foreach ($devices as $device) {
            $deviceReviews = $device->getReviews();
            $deviceReviewsCount = count($deviceReviews) > 0 ? count($deviceReviews) : 0;
            $deviceReviewsRatings = [];
            $deviceAverageRating = 0;

            $deviceOrders = $this->em->getRepository(OrderDetails::class)->findBy(['product' => $device->getTitle()], ['product' => 'ASC']);
            $deviceOrdersTotal = 0;
            $deviceOrdersRevenue = 0;

            if (count($deviceOrders) > 0) {
                foreach ($deviceOrders as $deviceOrder) {
                    $deviceOrdersTotal += $deviceOrder->getQuantity();
                    $deviceOrdersRevenue += $deviceOrder->getTotalPrice();
                }
            }

            foreach ($deviceReviews as $review) {
                $reviewRating = $review->getRating();
                array_push($deviceReviewsRatings, $reviewRating);
            }

            foreach ($deviceReviewsRatings as $rating) {
                $deviceAverageRating =+ $rating;
            }

            if (count($deviceReviewsRatings) > 0) {
                $deviceAverageRating += count($deviceReviewsRatings);
            } else $deviceAverageRating = 5;

            array_push($devicesChartData, [$device->getTitle(), $deviceReviewsCount, $deviceAverageRating, $deviceOrdersTotal, $deviceOrdersRevenue]);
        }

        $chart = new BarChart();
        $chart->getData()->setArrayToDataTable($devicesChartData);
        $chart->getOptions()
            ->setTitle('Devices')
            ->setWidth(900)
            ->setHeight(500)
            ->setSeries([['axis' => 'Reviews'], ['axis' => 'Rating'], ['axis' => 'Orders'], ['axis' => 'Revenue']])
            ->setAxes(['x' => [
                'Reviews' => ['side' => 'top', 'label' => 'Reviews'],
                'Rating' => ['side' => 'top', 'label' => 'Rating'],
                'Orders' => ['side' => 'bottom', 'label' => 'Orders'],
                'Revenue' => ['side' => 'bottom', 'label' => 'Revenue']
            ]]);

        return $chart;
    }
}
