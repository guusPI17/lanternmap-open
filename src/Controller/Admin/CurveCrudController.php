<?php

namespace App\Controller\Admin;

use App\Entity\Curve;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CurveCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Curve::class;
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
