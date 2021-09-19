<?php

namespace App\Controller\Admin;

use App\Entity\Catalog;
use App\Entity\Brand;
use App\Entity\Model;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use phpDocumentor\Reflection\Types\Parent_;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Catalog::class);
        $query = $repository->createQueryBuilder('c')
            ->innerJoin(Brand::class, 'b', 'with', 'c.brandId = b.id')
            ->innerJoin(Model::class, 'm', 'with', 'c.modelId = m.id')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/index.html.twig', [
            'title' => 'Каталог авто',
            'data' => $query
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Каталог авто');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Панель администратора', 'fa fa-shield-alt');
        yield MenuItem::linktoRoute('Вернуться на сайт', 'fas fa-home', 'home');
        yield MenuItem::linktoDashboard('Каталог авто', 'fa fa-th-list', Catalog::class);
        yield MenuItem::linktoDashboard('Бренды', 'fas fa-car', Brand::class);
        yield MenuItem::linktoDashboard('Модели', 'fas fa-car', Model::class);
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
