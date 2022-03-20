<?php

namespace Macareux\MdFileTextExtractor\Entity\Attribute\Value\Value;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atMacareuxFilePageText")
 */
class FilePageTextValue
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="FileTextValue", inversedBy="pages")
     * @ORM\JoinColumn(name="avID", referencedColumnName="avID")
     */
    protected $value;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $page;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $text;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAttributeValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setAttributeValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }
}