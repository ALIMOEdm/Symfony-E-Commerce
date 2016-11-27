<?php
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
* @ORM\Entity
* @ORM\Table(name="category_treepath")
*/
class Category_treepath{
    /**
    * @ORM\Column(name="tree_id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    public $tree_id;
    /**
    * @ORM\Column(name="ancestor", type="integer")
    * @ORM\OneToOne(targetEntity="Category", inversedBy="category_id")
    * @ORM\JoinColumn(name="ancestor", referencedColumnName="category_id")
    */
    public $ancestor;
    /**
    * @ORM\Column(name="descendant", type="integer")
    * @ORM\ManyToOne(targetEntity="Category", inversedBy="category_id")
    * @ORM\JoinColumn(name="descendant", referencedColumnName="category_id")
    */
    public $descendant; 
	/**
	* @ORM\Column(name="level", type="integer")
    */
	public $level;

	public function __construct(){
	}

    /**
     * Get treeId
     *
     * @return integer
     */
    public function getTreeId()
    {
        return $this->tree_id;
    }

    /**
     * Set ancestor
     *
     * @param integer $ancestor
     *
     * @return Category_treepath
     */
    public function setAncestor($ancestor)
    {
        $this->ancestor = $ancestor;

        return $this;
    }

    /**
     * Get ancestor
     *
     * @return integer
     */
    public function getAncestor()
    {
        return $this->ancestor;
    }

    /**
     * Set descendant
     *
     * @param integer $descendant
     *
     * @return Category_treepath
     */
    public function setDescendant($descendant)
    {
        $this->descendant = $descendant;

        return $this;
    }

    /**
     * Get descendant
     *
     * @return integer
     */
    public function getDescendant()
    {
        return $this->descendant;
    }

    /**
     * Set level
     *
     * @param integer $level
     *
     * @return Category_treepath
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }
}
