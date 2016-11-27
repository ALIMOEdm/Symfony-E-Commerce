<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
* @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\ExtraFieldsRepository")
* @ORM\HasLifecycleCallbacks
* @ORM\Table(name="extra_fields")
*/
class Extra_fields{

	/**
	* @ORM\Column(name="id", type="integer")
	* @ORM\Id
	* @ORM\GeneratedValue(strategy="AUTO")
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
    * @ORM\Column(name="type", columnDefinition="ENUM('number', 'text', 'date')")
    */
    public $type;
    /**
    * @ORM\Column(name="show_it", type="boolean")
    */
    public $show_it = false;
    /**
    * @ORM\Column(name="showcase", type="boolean")
    */
    public $showcase = false;
	/**
	* @ORM\Column(name="created_at", type="datetime")
    */
    public $created_at;
    /**
    * @ORM\Column(name="updated_at", type="datetime")
	*/
    public $updated_at;

     /**
     * @ORM\ManyToMany(targetEntity="Category", mappedBy="extra_fields")
     * @ORM\JoinTable(name="categories_fields")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="GoodExtraField", mappedBy="extra_field")
     */
    private $extra_fields;

    private $value = '';
    public function setValue($value){
        $this->value = $value;
    }
    public function getValue(){
        return $this->value;
    }

    public function __construct() {
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get fieldId
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString(){
       return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Extra_fields
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
     * Set xmlTitle
     *
     * @param string $xmlTitle
     *
     * @return Extra_fields
     */
    public function setXmlTitle($xmlTitle)
    {
        $this->xml_title = $xmlTitle;

        return $this;
    }

    /**
     * Get xmlTitle
     *
     * @return string
     */
    public function getXmlTitle()
    {
        return $this->xml_title;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Extra_fields
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set show_it
     *
     * @param string $show_it
     *
     * @return Extra_fields
     */
    public function setShowIt($show_it)
    {
        $this->show_it = $show_it;

        return $this;
    }

    /**
     * Get show_it
     *
     * @return string
     */
    public function getShowIt()
    {
        return $this->show_it;
    }

    /**
     * Set showcase
     *
     * @param string $showcase
     *
     * @return Extra_fields
     */
    public function setShowcase($showcase)
    {
        $this->showcase = $showcase;

        return $this;
    }

    /**
     * Get showcase
     *
     * @return string
     */
    public function getShowcase()
    {
        return $this->showcase;
    }

    /**
     * Set createdAt
     *
     * @param string $createdAt
     *
     * @return Extra_fields
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return string
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
     * @return Extra_fields
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
     * Add category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return Extra_fields
     */
    public function addCategory(\AppBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \AppBundle\Entity\Category $category
     */
    public function removeCategory(\AppBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
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

    /**
     * Add extraField
     *
     * @param \AppBundle\Entity\GoodExtraField $extraField
     *
     * @return Extra_fields
     */
    public function addExtraField(\AppBundle\Entity\GoodExtraField $extraField)
    {
        $this->extra_fields[] = $extraField;

        return $this;
    }

    /**
     * Remove extraField
     *
     * @param \AppBundle\Entity\GoodExtraField $extraField
     */
    public function removeExtraField(\AppBundle\Entity\GoodExtraField $extraField)
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
}
