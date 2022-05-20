<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Entity;

use App\Project\Infrastructure\Repository\TemplateTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass=TemplateTypeRepository::class)
 */
class TemplateType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private string $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $class;

    /**
     * @ORM\OneToMany(targetEntity=TemplateImport::class, mappedBy="templateType")
     */
    private Collection $templateImports;

    public function __construct(UuidInterface $id)
    {
        $this->id              = $id->toString();
        $this->templateImports = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return Collection<int, TemplateImport>
     */
    public function getTemplateImports(): Collection
    {
        return $this->templateImports;
    }

    public function addTemplateImport(TemplateImport $templateImport): self
    {
        if (!$this->templateImports->contains($templateImport)) {
            $this->templateImports[] = $templateImport;
            $templateImport->setTemplateType($this);
        }

        return $this;
    }

    public function removeTemplateImport(TemplateImport $templateImport): self
    {
        if ($this->templateImports->removeElement($templateImport)) {
            // set the owning side to null (unless already changed)
            if ($templateImport->getTemplateType() === $this) {
                $templateImport->setTemplateType(null);
            }
        }

        return $this;
    }
}
