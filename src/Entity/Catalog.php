<?php

namespace App\Entity;

use App\Repository\CatalogRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="`catalog`")
 * @ORM\Entity(repositoryClass=CatalogRepository::class)
 */
class Catalog
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(name="roule", type="boolean")
     */
    private bool $roule;

    /**
     * @ORM\ManyToOne(targetEntity=Brand::class, inversedBy="catalogs")
     */
    private Brand $brandId;

    /**
     * @ORM\ManyToOne(targetEntity=Model::class)
     */
    private Model $modelId;


    public function getId(): int
    {
        return $this->id;
    }

    public function getRoule(): bool
    {
        return $this->roule;
    }

    public function setRoule(bool $roule): self
    {
        $this->roule = $roule;

        return $this;
    }

    public function getBrandId(): Brand
    {
        return $this->brandId;
    }

    public function setBrandId(Brand $brandId ): self
    {
        $this->brandId = $brandId;

        return $this;
    }

    public function getModelId(): Model
    {
        return $this->modelId;
    }

    public function setModelId(Model $modelId): self
    {
        $this->modelId = $modelId;

        return $this;
    }

    public function __toString() {
        return $this;
    }
}
