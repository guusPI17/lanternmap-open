<?php

namespace App\Controller\Admin;

use App\Entity\Curve;
use App\Entity\Lantern;
use App\Entity\LanternType;
use App\Entity\Locality;
use App\Entity\Map;
use App\Entity\StreetClass;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
        $url = $routeBuilder->setController(UserCrudController::class)->generateUrl();

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('App');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Curve', 'fas fa-list', Curve::class);
        yield MenuItem::linkToCrud('Lantern', 'fas fa-list', Lantern::class);
        yield MenuItem::linkToCrud('LanternType', 'fas fa-list', LanternType::class);
        yield MenuItem::linkToCrud('Locality', 'fas fa-list', Locality::class);
        yield MenuItem::linkToCrud('Map', 'fas fa-list', Map::class);
        yield MenuItem::linkToCrud('StreetClass', 'fas fa-list', StreetClass::class);
        yield MenuItem::linkToCrud('User', 'fas fa-list', User::class);
    }
}
