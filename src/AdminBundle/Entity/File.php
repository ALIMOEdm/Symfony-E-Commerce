<?php
namespace AdminBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Entity
* @ORM\HasLifecycleCallbacks
* @ORM\Table(name="file")
*/
class File{
   /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Goods", inversedBy="images")
     * @ORM\JoinColumn(name="good_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $good_id;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isImageUpdate = 1;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @Assert\File(
     *     mimeTypes = {
     *      "image/jpeg", "image/pipeg",
     *   },
     *     mimeTypesMessage = "Please upload a valid JPEG file"
     * )
     */
    private $file;

//    /**
//     * @Assert\File(
//     *     mimeTypes = {
//     *      "image/jpeg", "image/pipeg",
//     *   },
//     *     mimeTypesMessage = "Please upload a valid JPEG file"
//     * )
//     */
    private $files;

    public function getFiles(){
        return $this->files;
    }

    public function setFiles($files){
        $this->files = $files;
    }

    public function getAbsolutePath()
    {
        return null === $this->path
            ? null
            : $this->getUploadRootDir().'/'.$this->path;
    }

    public function getWebPath()
    {
        return null === $this->path
            ? null
            : $this->getUploadDir().'/'.$this->path;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded
        // documents should be saved
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return 'uploads/images';
    }

    public function getUploadDirWithName()
    {
        return $this->getUploadRootDir().'/'.$this->getPath().'/'.$this->getName();
    }

    private $temp;

    /**
     * Sets file.
     *
     * @param $file
     */
    //UploadedFile
    public function setFile($file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (isset($this->path)) {
            // store the old name to delete after the update
            $this->temp = $this->path;
            $this->path = null;
        } else {
            $this->path = $this->getUploadDir();
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename.'.'.$this->getFile()->guessExtension();

        }
    }

    /**
     * @ORM\PreRemove()
     */
    public function removeUpload()
    {
        $file = $this->getFullFilePath();
        if ($file && file_exists($file)) {
            unlink($file);
        }
    }

    public function getFullFilePath()
    {
        return $this->getAbsolutePath()."/".$this->getName();
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

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getAbsolutePath(), $this->name);

        // check if we have an old image
        if (isset($this->temp)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->temp);
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    public function getFile()
    {
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return File
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
     * @return File
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
     * Set isImageUpdate
     *
     * @param boolean $isImageUpdate
     *
     * @return File
     */
    public function setIsImageUpdate($isImageUpdate)
    {
        $this->isImageUpdate = $isImageUpdate;

        return $this;
    }

    /**
     * Get isImageUpdate
     *
     * @return boolean
     */
    public function getIsImageUpdate()
    {
        return $this->isImageUpdate;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return File
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return File
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
     * Set goodId
     *
     * @param \AdminBundle\Entity\Goods $goodId
     *
     * @return File
     */
    public function setGoodId(\AdminBundle\Entity\Goods $goodId = null)
    {
        $this->good_id = $goodId;

        return $this;
    }

    /**
     * Get goodId
     *
     * @return \AdminBundle\Entity\Goods
     */
    public function getGoodId()
    {
        return $this->good_id;
    }

    public function __toString(){
        return $this->getName();
    }

    public function createCatalogInUploadDir()
    {
        if ($handle = @opendir($this->getAbsolutePath())) {
            return true;
        }
        if (!mkdir($this->getAbsolutePath(), 0777, true)) {
            throw new Exception('Не удалось создать каталог для сохранения изображений');
        }
        return true;
    }
}
