<?php

namespace Sowp\BudgetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sowp\BudgetBundle\Repository\ContractRepository")
 * @Serializer\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "sowp_budget_contract_api_get_contract",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute=true
 *      ),
 *     exclusion=@Hateoas\Exclusion(
 *          maxDepth=1,
 *     )
 * )
*/
class Contract
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="contracts")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     * @Serializer\Expose()
     */
    private $category;

    /**
     * @ORM\Column(type="date")
     * @Serializer\Expose()
     */
    private $conclusionAt;

    /**
     * @ORM\Column(length=255)
     * @Serializer\Expose()
     */
    private $supplier;

    /**
     * @ORM\Column(length=255)
     * @Serializer\Expose()
     */
    private $title;

    /**
     * @ORM\Column(length=64)
     * @Serializer\Expose()
     */
    private $value;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set conclusionAt
     *
     * @param \DateTime $conclusionAt
     *
     * @return Contract
     */
    public function setConclusionAt($conclusionAt)
    {
        $this->conclusionAt = $conclusionAt;

        return $this;
    }

    /**
     * Get conclusionAt
     *
     * @return \DateTime
     */
    public function getConclusionAt()
    {
        return $this->conclusionAt;
    }

    /**
     * Set supplier
     *
     * @param string $supplier
     *
     * @return Contract
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * Get supplier
     *
     * @return string
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Contract
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return Contract
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set category
     *
     * @param \Sowp\BudgetBundle\Entity\Category $category
     *
     * @return Contract
     */
    public function setCategory(\Sowp\BudgetBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Sowp\BudgetBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

   /**
     * Return string representation of object
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }
}
