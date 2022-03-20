<?php

namespace Macareux\MdFileTextExtractor\Entity\Attribute\Value\Value;

use Concrete\Core\Entity\Attribute\Value\Value\AbstractValue;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="atMacareuxFileText")
 */
class FileTextValue extends AbstractValue
{
    /**
     * @var FilePageTextValue[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="FilePageTextValue",
     *     cascade={"persist", "remove"}, mappedBy="value")
     * @ORM\JoinColumn(name="avID", referencedColumnName="avID")
     */
    protected $pages;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|FilePageTextValue[]
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param ArrayCollection|FilePageTextValue[] $pages
     */
    public function setPages($pages): void
    {
        $this->pages = $pages;
    }

    public function __toString()
    {
        $texts = [];
        foreach ($this->getPages() as $page) {
            $texts[] = $page->getText();
        }

        return implode("\n", $texts);
    }

    public function __clone()
    {
        if ($this->generic_value) {
            $clonedPages = new ArrayCollection();
            foreach ($this->getPages() as $page) {
                $clonedPage = clone $page;
                $clonedPage->setAttributeValue($this);
                $clonedPages->add($clonedPage);
            }
            $this->setPages($clonedPages);
        }
    }
}