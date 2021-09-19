<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use App\Entity\Catalog;
use App\Entity\Model;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class CatalogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Catalog::class;
    }

    #[Route('/admin/catalog', name: 'catalog')]
    public function catalog(): Response
    {

        $repository = $this->getDoctrine()->getRepository(Catalog::class);

        $query = $repository->createQueryBuilder('c')
            ->innerJoin(Brand::class, 'b', 'with', 'c.brandId = b.id')
            ->innerJoin(Model::class, 'm', 'with', 'c.modelId = m.id')
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/index.html.twig', [
            'title' => 'Панель - Каталог авто',
            'admin' => true,
            'data' => $query
        ]);
    }

    #[Route('/admin/catalog/create', name: 'catalogCreate')]
    public function catalogCreate(Request $request): Response
    {

        return $this->render('admin/create.html.twig', [
            'title' => 'Панель - Добавить в каталог',
            'admin' => true
        ]);
    }

    #[Route('/admin/catalog/edit', name: 'catalogItemEdit')]
    public function catalogItemEdit(Request $request): Response
    {
        $data = $request->toArray();
        $validator = (new \App\Controller\ValidatorController)->validator($data, ['required']);


        if ($validator) {
            return new Response(json_encode(
                [
                    'status' => 'false',
                    'errors' => $validator
                ]
            ));
        }

        $IdBrand = $this->checkItemRaw($data['brand'], Brand::class)->getId();
        $IdModel = $this->checkItemRaw($data['model'], Model::class)->getId();

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository(Catalog::class)->createQueryBuilder('')
            ->update(Catalog::class, 't')
            ->set('t.brandId', ':idBrand')
            ->set('t.modelId', ':idModel')
            ->set('t.roule', ':roule')
            ->setParameter('idBrand', $IdBrand)
            ->setParameter('idModel', $IdModel)
            ->setParameter('roule', $data['roule'])

            ->where('t.id = :id')
            ->setParameter('id', $data['index'])
            ->getQuery();
        $query->getArrayResult();

        return new Response(json_encode(
            [
                'status' => 'true',
                'errors' => $validator
            ]
        ));
    }

    public function getEntity($id, $thisClass) {
        $repository = $this->getDoctrine()->getRepository($thisClass);
        $query = $repository->createQueryBuilder('t')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
        return $query[0];
    }
    public function checkItemRaw($name, $thisClass) {
        $repository = $this->getDoctrine()->getRepository($thisClass);
        $query = $repository->createQueryBuilder('t')
            ->where('t.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getResult();
            //->getFirstResult();
        if ($query) return $query[0];
        return $this->createItemRaw($name, $thisClass);
    }

    public function createItemRaw($name, $thisClass) {
        $entityManager = $this->getDoctrine()->getManager();
        $query = new $thisClass();
        $query->setName($name);
        $entityManager->persist($query);
        $entityManager->flush();
        return $query;
    }

    #[Route('/admin/catalog/rm/{id}', name: 'catalogItemRemove')]
    public function catalogItemRemove(string $id = null): Response
    {
        if ($id) {
            $repository = $this->getDoctrine()->getRepository(Catalog::class);
            $query = $repository->createQueryBuilder('c')
                ->where('c.id = :id' )
                ->setParameter('id', $id)
                ->delete()
                ->getQuery()
                ->getResult();
        }

        return new Response(json_encode(['status' => 'true' ]));
    }

    #[Route('/admin/catalog/create', name: 'catalogCreate')]
    public function catalogItemCreate(Request $request): Response
    {
        $data = $request->toArray();
        $validator = (new \App\Controller\ValidatorController)->validator($data, ['required']);

        if ($validator) {
            return new Response(json_encode(
                [
                    'status' => 'false',
                    'errors' => $validator
                ]
            ));
        }
        $brand = $this->checkItemRaw($data['brand'], Brand::class);
        $model = $this->checkItemRaw($data['model'], Model::class);

        $em  = $this->getDoctrine()->getManager();
        $query = new Catalog();
        $query->setBrandId($brand);
        $query->setModelId($model);
        $query->setRoule($data['roule']);
        $em->persist($query);
        $em->flush();

        return new Response(json_encode(
            [
                'status' => 'true',
                'errors' => $validator
            ]
        ));
    }


    #[Route('/admin/catalog/api/datafile', name: 'catalogDataFile')]
    public function createDataFile(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Catalog::class);
        $query = $repository->createQueryBuilder('c')
            ->select('c.id, b.name as nameBrand, m.name as nameModel, c.roule')
            ->innerJoin(Brand::class, 'b', 'with', 'c.brandId = b.id')
            ->innerJoin(Model::class, 'm', 'with', 'c.modelId = m.id')
            ->orderBy('c.id', 'ASC')
            ->getQuery();
        $data = $query->getArrayResult();
        $filed = "data.json";
        $path = $_SERVER["DOCUMENT_ROOT"] . 'data/'.$filed;
        file_put_contents($path, json_encode($data));

        return new Response(json_encode(['file' => $filed ]));
    }

}





