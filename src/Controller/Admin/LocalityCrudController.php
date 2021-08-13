<?php

namespace App\Controller\Admin;

use App\Entity\Locality;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LocalityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Locality::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $viewStripeInvoice = Action::new('viewInvoice', 'Show', 'fa fa-file-invoice')
            ->linkToUrl(function (Locality $entity) {
                return 'uploads/dataMovement/' . $entity->getDataMovement();
            });
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, $viewStripeInvoice);
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }*/

}
