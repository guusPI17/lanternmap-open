<?php

namespace App\Controller\Admin;

use App\Entity\LanternType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LanternTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LanternType::class;
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
