<?php

namespace App\Controller\Admin;

use App\Entity\Request;
use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

class RequestCrudController extends AbstractCrudController
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
        return Request::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            TextField::new('requested_by')->hideOnForm(),
            ChoiceField::new('status')->setChoices(Request::getStatusSelection()),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $approveAction = Action::new('approve', 'Approve')
            ->linkToCrudAction('approveAction')
            ->displayIf(static function (Request $request) {
                $requestStatus = $request->getStatus();
                $condition = $requestStatus == 'Pending' or $requestStatus == 'Rejected';
                if ($requestStatus == 'Pending' or $requestStatus == 'Rejected') {
                    return true;
                } elseif ($requestStatus == 'Approved') {
                    return false;
                } else return true;
            });

        $rejectAction = Action::new('reject', 'Reject')
            ->linkToCrudAction('rejectAction')->addCssClass('text-danger')
            ->displayIf(static function (Request $request) {
                $requestStatus = $request->getStatus();
                $condition = $requestStatus == 'Pending';
                return $condition;
            });

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_INDEX, $rejectAction)
            ->add(Crud::PAGE_INDEX, $approveAction)
        ;
    }

    public function approveAction(AdminContext $context)
    {
        $userRequest = $context->getEntity()->getInstance();

        if ($userRequest instanceof Request) {
            $userRequest->setStatus('Approved');
        }

        $newProduct = new Product();
        $newProduct
            ->setTitle($userRequest->getTitle())
            ->setPrice(0)
            ->setType('Game');

        $this->em->persist($newProduct);
        $this->em->flush();

        $url = $this->adminUrlGenerator
                ->setController(ProductCrudController::class)
                ->setAction(Crud::PAGE_EDIT)
                ->setEntityId($newProduct->getId())
                ->generateUrl();

        return $this->redirect($url);;
    }

    public function rejectAction(AdminContext $context)
    {
        $userRequest = $context->getEntity()->getInstance();

        if ($userRequest instanceof Request) {
            $userRequest->setStatus('Rejected');
            $this->em->flush();
        }

        $url = $this->adminUrlGenerator
                ->setController(RequestCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl();

        return $this->redirect($url);;
    }
}
