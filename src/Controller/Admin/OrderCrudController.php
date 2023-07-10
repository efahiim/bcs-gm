<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    private AdminUrlGenerator $adminUrlGenerator;
    private EntityManagerInterface $em;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $em)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->em = $em;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->setFormTypeOption('disabled','disabled'),
            AssociationField::new('user')->setFormTypeOption('disabled','disabled'),
            TextField::new('reference')->setFormTypeOption('disabled','disabled'),
            // AssociationField::new('orderDetails')->setFormTypeOption('disabled','disabled'),
            ArrayField::new('orderDetails')->hideOnForm(),
            TextField::new('delivery_address')->setFormTypeOption('disabled','disabled'),
            DateTimeField::new('ordered_on')->setFormat('short', 'short')->setFormTypeOption('disabled','disabled'),
            TextField::new('paypal_id')->setFormTypeOption('disabled','disabled'),
            MoneyField::new('total')->setCurrency('USD')->setFormTypeOption('disabled','disabled'),
            ChoiceField::new('status')->setChoices(Order::getStatusSelection())
        ];
    }
    
    public function configureActions(Actions $actions): Actions
    {
        $approveAction = Action::new('approve', 'Approve')
            ->linkToCrudAction('approveAction')
            ->displayIf(static function (Order $order) {
                $orderStatus = $order->getStatus();
                $condition = $orderStatus == 'Paid';
                return $condition;
            });

        $cancelAction = Action::new('cancel', 'Cancel')
            ->linkToCrudAction('cancelAction')->addCssClass('text-danger')
            ->displayIf(static function (Order $order) {
                $orderStatus = $order->getStatus();
                $condition = $orderStatus == 'Cart' || $orderStatus == 'Paid';
                return $condition;
            });

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_INDEX, $cancelAction)
            ->add(Crud::PAGE_INDEX, $approveAction)
        ;
    }

    public function approveAction(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();

        if ($order instanceof Order) {
            $order->setStatus('Delivered');
            $this->em->flush();
        }

        $url = $this->adminUrlGenerator
                ->setController(OrderCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl();

        return $this->redirect($url);;
    }

    public function cancelAction(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();

        if ($order instanceof Order) {
            $order->setStatus('Cancelled');
            $orderDetails = $order->getOrderDetails();

            foreach ($orderDetails as $item) {
                $product = $this->em->getRepository(Product::class)->findOneBy(['title' => $item->getProduct()]);
                $productStock = $product->getStock();
                $product->setStock($productStock + $item->getQuantity());

                $this->em->persist($product);
            }

            $this->em->flush();
        }

        $url = $this->adminUrlGenerator
                ->setController(OrderCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl();

        return $this->redirect($url);;
    }
}
