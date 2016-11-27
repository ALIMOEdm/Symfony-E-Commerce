<?php
namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
* @ORM\Entity
* @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\GoodsRepository")
* @ORM\HasLifecycleCallbacks
* @ORM\Table(name="goods")
*/
class Goods{
    /**
    * @ORM\Column(name="id", type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    public $id;
    /**
    * @ORM\Column(name="short_title", type="string", length=255)
    */
    public $short_title = '';
    /**
    * @ORM\Column(name="title", type="string", length=255)
    */
    public $title = '';
    /**
    * @ORM\Column(name="xml_title", type="string", length=255)
    */
    public $xml_title = '';
    /**
    * @ORM\Column(name="description", type="text")
    */
    public $description = '';

    /**
    * @ORM\Column(name="brand", type="string", length=255)
    */
    public $brand = '';
    /**
    * @ORM\Column(name="article", type="string", length=255)
    */
    public $article = '';
    /**
    * @ORM\Column(name="rating", type="string", length=255)
    */
    public $rating = 0;

    /**
    * @ORM\Column(name="created_at", type="datetime")
    */
    public $created_at;
    /**
    * @ORM\Column(name="updated_at", type="datetime")
    */
    public $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="goods")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    public $category;

    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="good_id")
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="GoodExtraField", mappedBy="good")
     */
    private $extra_fields;

    /**
     * @Gedmo\Slug(fields={"title", "short_title"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string")
     */
    private $category_cache = '';

    private $extra_fields_cache = '';
    public function getExtraFieldsCache(){
        return $this->extra_fields_cache;
    }
    public function setExtraFieldsCache($str){
        $this->extra_fields_cache = $str;
    }

    //Тут будет храниться строка с изображениями
    private $images_stock = '';

    public function getImagesStock()
    {
        return $this->images_stock;
    }
    public function setImagesStock($images)
    {
        $this->images_stock = $images;
    }

    private $images_hidden_field = '';

    public function getImagesHiddenField()
    {
        return $this->images_hidden_field;
    }
    public function setImagesHiddenField($images)
    {
        $this->images_hidden_field = $images;
    }

    public function __construct() {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
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

    public function getObject(){
        return $this;
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
     * Set shortTitle
     *
     * @param string $shortTitle
     *
     * @return Goods
     */
    public function setShortTitle($shortTitle)
    {
        $this->short_title = $shortTitle;

        return $this;
    }

    /**
     * Get shortTitle
     *
     * @return string
     */
    public function getShortTitle()
    {
        return $this->short_title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Goods
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set brand
     *
     * @param string $brand
     *
     * @return Goods
     */
    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * Get brand
     *
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Set article
     *
     * @param string $article
     *
     * @return Goods
     */
    public function setArticle($article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return string
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Set rating
     *
     * @param string $rating
     *
     * @return Goods
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * Get rating
     *
     * @return string
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Add image
     *
     * @param \AppBundle\Entity\File $image
     *
     * @return Goods
     */
    public function addImage(\AppBundle\Entity\File $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \AppBundle\Entity\File $image
     */
    public function removeImage(\AppBundle\Entity\File $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return Goods
     */
    public function setCategory(\AppBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    public function removeAllImages($em){
        if(count($this->getImages())){
            $imgs = $this->getImages();
            foreach($imgs as $r){
                $this->removeImage($r);
                $em->remove($r);
            }
        }
    }

    public function removeAllExtraField($em){
        if(count($this->getExtraFields())){
            $fields = $this->getExtraFields();
            foreach($fields as $f){
                $this->removeExtraField($f);
                $em->remove($f);
            }
        }
    }

    public function checkImageExists($image_name){
        if(count($this->getImages())){
            foreach($this->getImages() as $image){
                if($image->getName() == $image_name){
                    return $image;
                }
            }
        }
        return false;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Goods
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add extraField
     *
     * @param \AppBundle\Entity\GoodExtraField $extraField
     *
     * @return Goods
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

    /**
     * Set categoryCache
     *
     * @param string $categoryCache
     *
     * @return Goods
     */
    public function setCategoryCache($categoryCache)
    {
        $this->category_cache = $categoryCache;

        return $this;
    }

    /**
     * Get categoryCache
     *
     * @return string
     */
    public function getCategoryCache()
    {
        return $this->category_cache;
    }
}
