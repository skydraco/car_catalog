<?php

namespace App\Controller;
use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\Catalog;
use App\Repository\CatalogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(CatalogRepository $catalogRepositoryitems): Response
    {
        $entityManager = EntityManager::class;
        $dql = "SELECT catalog.*,brand.*,model.*  FROM catalog
                INNER JOIN brand ON catalog.brandId = brandId
                INNER JOIN model ON catalog.modelId = modelId";

        //$query = $this->getDoctrine()->getManager()->createQuery($dql);
        //$q = $query->getQuery();
        //dd($q->getResult());
        $repository = $this->getDoctrine()->getRepository(Catalog::class);

        $query = $repository->createQueryBuilder('c')
            ->innerJoin(Brand::class, 'b', 'with', 'c.brandId = b.id')
            ->innerJoin(Model::class, 'm', 'with', 'c.modelId = m.id')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
        //dd($query);

        //$catalog = $catalogRepositoryitems->findAll();
        //dd($catalog);
        return $this->render('homepage/index.html.twig', [
            'title' => 'Каталог авто',
            'admin' => false,
            'data' => $query
        ]);
    }
}
