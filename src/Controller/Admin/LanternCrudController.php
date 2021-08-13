<?php

namespace App\Controller\Admin;

use App\Entity\Lantern;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LanternCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Lantern::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
