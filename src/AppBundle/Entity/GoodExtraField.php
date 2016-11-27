<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\GoodExtraFieldRepository")
 * @ORM\HasLifecycleCallbacks
 */
class GoodExtraField {
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @ORM\ManyToOne(targetEntity="Goods", inversedBy="extra_fields")
     * @ORM\JoinColumn(name="good_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $good;

    /**
     * @ORM\ManyToOne(targetEntity="Extra_fields", inversedBy="extra_fields")
     * @ORM\JoinColumn(name="extra_field_id", referencedColumnName="id")
     */
    private $extra_field;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $value = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $value_text = null;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $value_number = null;

    /**
     * @ORM\Column(type="string")
     */
    private $type = 'text';

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
     * Set good
     *
     * @param \AppBundle\Entity\Goods $good
     *
     * @return GoodExtraField
     */
    public function setGood(\AppBundle\Entity\Goods $good = null)
    {
        $this->good = $good;

        return $this;
    }

    /**
     * Get good
     *
     * @return \AppBundle\Entity\Goods
     */
    public function getGood()
    {
        return $this->good;
    }

    /**
     * Set extraField
     *
     * @param \AppBundle\Entity\Extra_fields $extraField
     *
     * @return GoodExtraField
     */
    public function setExtraField(\AppBundle\Entity\Extra_fields $extraField = null)
    {
        $this->extra_field = $extraField;

        return $this;
    }

    /**
     * Get extraField
     *
     * @return \AppBundle\Entity\Extra_fields
     */
    public function getExtraField()
    {
        return $this->extra_field;
    }

    /**
     * Set valueString
     *
     * @param string $valueString
     *
     * @return GoodExtraField
     */
    public function setValueString($valueString)
    {
        $this->value_text = $valueString;

        return $this;
    }

    /**
     * Get valueString
     *
     * @return string
     */
    public function getValueString()
    {
        return $this->value_text;
    }

    /**
     * Set valueInteger
     *
     * @param integer $valueInteger
     *
     * @return GoodExtraField
     */
    public function setValueInteger($valueInteger)
    {
        $this->value_number = $valueInteger;

        return $this;
    }

    /**
     * Get valueInteger
     *
     * @return integer
     */
    public function getValueInteger()
    {
        return $this->value_number;
    }

    public function setValue($extraFieldType, $value)
    {
        $value = ($value === "") ? null : $value;
        switch($extraFieldType){
            case 'number':
                $this->setType($extraFieldType);
                $this->setValueInteger($value);
                break;
            case 'text':
                $this->setType($extraFieldType);
                $this->setValueString($value);
                break;
            case 'date':
                break;
        }
    }

    public function getValue($extraFieldType)
    {
        switch($extraFieldType){
            case 'number':
                return $this->getValueInteger();
                break;
            case 'text':
                return $this->getValueString();
                break;
            case 'date':
                break;
        }
        return null;
    }

    public function getValueOwn()
    {
        switch($this->getType()){
            case 'number':
                return $this->getValueInteger();
                break;
            case 'text':
                return $this->getValueString();
                break;
            case 'date':
                break;
        }
        return null;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return GoodExtraField
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
     * Set valueText
     *
     * @param string $valueText
     *
     * @return GoodExtraField
     */
    public function setValueText($valueText)
    {
        $this->value_text = $valueText;

        return $this;
    }

    /**
     * Get valueText
     *
     * @return string
     */
    public function getValueText()
    {
        return $this->value_text;
    }

    /**
     * Set valueNumber
     *
     * @param string $valueNumber
     *
     * @return GoodExtraField
     */
    public function setValueNumber($valueNumber)
    {
        $this->value_number = $valueNumber;

        return $this;
    }

    /**
     * Get valueNumber
     *
     * @return string
     */
    public function getValueNumber()
    {
        return $this->value_number;
    }
}
