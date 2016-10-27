<?php

namespace Sowp\BudgetBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Sowp\BudgetBundle\Repository\CategoryRepository")
 * @Serializer\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "sowp_budget_category_api_get_category",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute=true
 *      ),
 *     exclusion=@Hateoas\Exclusion(
 *          maxDepth=1,
 *     )
 * )
 *
 * @Hateoas\Relation(
 *      "children",
 *      href = @Hateoas\Route(
 *          "sowp_budget_category_api_get_category_children",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute=true
 *      ),
 *     exclusion=@Hateoas\Exclusion(
 *          maxDepth=1,
 *     )
 * )
 *
 * @Hateoas\Relation(
 *      "parent",
 *      href = @Hateoas\Route(
 *          "sowp_budget_category_api_get_category",
 *          parameters = { "id" = "expr(object.getParent().getId())" },
 *          absolute=true
 *      ),
 *     exclusion=@Hateoas\Exclusion(
 *          excludeIf = "expr(null === object.getParent())"
 *     )
 * )
 *
 * @Hateoas\Relation(
 *      "contracts",
 *      href = @Hateoas\Route(
 *          "sowp_budget_category_api_get_category_contracts",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute=true
 *      ),
 *     exclusion=@Hateoas\Exclusion(
 *          maxDepth=1,
 *     )
 * )
 */
class Category
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     */
    private $id;

    /**
     * @ORM\Column(length=64, nullable=false)
     * @Assert\NotNull()
     * @Serializer\Expose()
     * @Serializer\Groups({"list", "detail"})
     */
    private $title;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRoot
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity="Contract", mappedBy="category")
     */
    private $contracts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->contracts = new ArrayCollection();
    }

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
     * set title
     *
     * @param $title
     *
     * @return $this
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
     * Set lft
     *
     * @param integer $lft
     *
     * @return Category
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     *
     * @return Category
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     *
     * @return Category
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set root
     *
     * @param \Sowp\BudgetBundle\Entity\Category $root
     *
     * @return Category
     */
    public function setRoot(Category $root = null)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * @return Category
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param Category|null $parent
     *
     * @return $this
     */
    public function setParent(Category $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Category
     */
    public function getParent()
    {
        return $this->parent;
    }
    /**
     * Add child
     *
     * @param \Sowp\BudgetBundle\Entity\Category $child
     *
     * @return Category
     */
    public function addChild(Category $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \Sowp\BudgetBundle\Entity\Category $child
     *
     * @return $this
     */
    public function removeChild(Category $child)
    {
        $this->children->removeElement($child);

        return $this;
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add contract
     *
     * @param \Sowp\BudgetBundle\Entity\Contract $contract
     *
     * @return Category
     */
    public function addContract(Contract $contract)
    {
        $this->contracts[] = $contract;

        return $this;
    }

    /**
     * Remove contract
     *
     * @param \Sowp\BudgetBundle\Entity\Contract $contract
     *
     * @return $this
     */
    public function removeContract(Contract $contract)
    {
        $this->contracts->removeElement($contract);

        return $this;
    }

    /**
     * Get contracts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContracts()
    {
        return $this->contracts;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getTitle();
    }
}
