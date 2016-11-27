<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Entity
* @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\CategoryRepository")
* @ORM\HasLifecycleCallbacks
* @ORM\Table(name="category")
*/
class Category{
    /**
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\OneToOne(targetEntity="Category_treepath", mappedBy="ancestor")
    * @ORM\OneToMany(targetEntity="Category_treepath", mappedBy="descendant")
    * @ORM\OneToMany(targetEntity="Category", mappedBy="parent_category")
    */
	public $id;
    /**
    * @ORM\Column(name="title", type="string", length=255)
    */
    public $title;
	/**
	* @ORM\Column(name="xml_title", type="string", length=255)
	*/
	public $xml_title;
	/**
	* @ORM\Column(name="content", type="text")
	*/
	public $content;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    public $path;
	/**
	* @ORM\Column(name="created_at", type="datetime")
	*/
	public $created_at;
	/**
	* @ORM\Column(name="updated_at", type="datetime")
	*/
    public $updated_at;

    /**
    * @ORM\Column(name="parent_category", type="integer", nullable=true)
    * @ORM\ManyToOne(targetEntity="Category", inversedBy="id")
    * @ORM\JoinColumn(name="parent_category", referencedColumnName="id")
    */
    public $parent_category;
    public $child_categories;
    
    private $parent_category_entity;
    public function getParentCategoryEntity(){
        return $this->parent_category_entity;
    }
    public function setParentCategoryEntity2($category = null){
        $this->parent_category_entity = $category;
    }

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;
    private $temp;

    /**
     * @ORM\ManyToMany(targetEntity="Extra_fields", inversedBy="categories")
     * @ORM\JoinTable(name="category_fields")
     */
    private $extra_fields;

    /**
     * @ORM\OneToMany(targetEntity="Goods", mappedBy="category")
     */
    private $goods;

    public function __construct() {
        $this->child_categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->extra_fields = new \Doctrine\Common\Collections\ArrayCollection();
        $this->features = new \Doctrine\Common\Collections\ArrayCollection();
    }


       /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (is_file($this->getAbsolutePath())) {
            // store the old name to delete after the update
            $this->temp = $this->getAbsolutePath();
            $this->path = null;
        } else {
            $this->path = 'initial';
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            $this->path = $this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->temp);
            // clear the temp image path
            $this->temp = null;
        }

        // you must throw an exception here if the file cannot be moved
        // so that the entity is not persisted to the database
        // which the UploadedFile move() method does
        $this->getFile()->move(
            $this->getUploadRootDir(),
            $this->id.'.'.$this->getFile()->guessExtension()
        );

        $this->setFile(null);
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (isset($this->temp)) {
            unlink($this->temp);
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->id.'.'.$this->path;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile(){
        return $this->file;
    }



     /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now'));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

	public function __toString(){
	   return $this->title;
    }

    // public function getAbsolutePath(){
    //     return null === $this->path
    //         ? null
    //         : $this->getUploadRootDir().'/'.$this->path;
    // }

    public function getWebPath(){
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir(){
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir(){
        return 'uploads/images';
    }

    public function getObject(){
        return $this;
    }
    /**
     * Set parent
     */
    public function setParentCategory($parent)
    {
        $this->parent_category =  is_null($parent) ? null : $parent->getCategoryId();
        return $this;
    }

    /**
     * Set parent entity
     */
    public function setParentCategoryEntity($parent)
    {
        $this->parent_category = $parent;
        return $this;
    }

    /**
     * Get parent
     *
     * @return integer
     */
    public function getParentCategory()
    {
        return $this->parent_category;
    }


    /**
     * Get categoryId
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * Get categoryId
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Category
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
     * Set xmltitle
     *
     * @param string $xml_title
     *
     * @return Category
     */
    public function setXmlTitle($xmltitle)
    {
        $this->xml_title = $xmltitle;

        return $this;
    }

    /**
     * Get xmltitle
     *
     * @return string
     */
    public function getXmlTitle()
    {
        return $this->xml_title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Category
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Category
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Category
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return Category
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Add childCategory
     *
     * @param $childCategory
     *
     * @return Category
     */
    public function addChildCategory($childCategory)
    {
        $this->child_categories[] = $childCategory;

        return $this;
    }

    /**
     * Remove childCategory
     *
     * @param $childCategory
     */
    public function removeChildCategory($childCategory)
    {
        $this->child_categories->removeElement($childCategory);
    }

    /**
     * Get childCategory
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildCategories()
    {
        return $this->child_categories;
    }

    /**
     * Add extraField
     *
     * @param \AppBundle\Entity\Extra_fields $extraField
     *
     * @return Category
     */
    public function addExtraField(\AppBundle\Entity\Extra_fields $extraField)
    {
        $this->extra_fields[] = $extraField;

        return $this;
    }

    /**
     * Remove extraField
     *
     * @param \AppBundle\Entity\Extra_fields $extraField
     */
    public function removeExtraField(\AppBundle\Entity\Extra_fields $extraField)
    {
        $this->extra_fields->removeElement($extraField);
    }

    /**
     * Get extraFields
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getExtraFields()
    {
        return $this->extra_fields;
    }

    /**
     * Add good
     *
     * @param \AppBundle\Entity\Goods $good
     *
     * @return Category
     */
    public function addGood(\AppBundle\Entity\Goods $good)
    {
        $this->goods[] = $good;

        return $this;
    }

    /**
     * Remove good
     *
     * @param \AppBundle\Entity\Goods $good
     */
    public function removeGood(\AppBundle\Entity\Goods $good)
    {
        $this->goods->removeElement($good);
    }

    /**
     * Get goods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGoods()
    {
        return $this->goods;
    }
}
