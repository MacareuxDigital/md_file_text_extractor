<?php

namespace Concrete\Package\MdFileTextExtractor\Attribute\FileText;

use Concrete\Core\Attribute\Controller as AttributeController;
use Concrete\Core\Attribute\FontAwesomeIconFormatter;
use Concrete\Core\Entity\Attribute\Value\AbstractValue;
use Concrete\Core\Form\Service\Form;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Macareux\MdFileTextExtractor\Entity\Attribute\Value\Value\FilePageTextValue;
use Macareux\MdFileTextExtractor\Entity\Attribute\Value\Value\FileTextValue;
use voku\helper\UTF8;

class Controller extends AttributeController
{
    protected $searchIndexFieldDefinition = [
        'type' => 'text',
        'options' => ['default' => null, 'notnull' => false],
    ];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\AttributeInterface::getIconFormatter()
     */
    public function getIconFormatter()
    {
        return new FontAwesomeIconFormatter('file-text');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\AttributeInterface::getAttributeValueClass()
     */
    public function getAttributeValueClass()
    {
        return FileTextValue::class;
    }

    /**
     * @return AbstractValue|null
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function getAttributeValueObject()
    {
        return $this->attributeValue ? $this->entityManager->find(FileTextValue::class, $this->attributeValue->getGenericValue()) : null;
    }

    public function searchForm($list)
    {
        $value = $this->request('value');
        if ($value) {
            $list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), '%' . $value . '%', 'like');
        }

        return $list;
    }

    public function search()
    {
        /** @var Form $f */
        $f = $this->app->make('form');
        echo $f->text($this->field('value'), $this->request('value'));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Attribute\AttributeInterface::createAttributeValue()
     */
    public function createAttributeValue($mixed)
    {
        if ($mixed instanceof FileTextValue) {
            return clone $mixed;
        }
        if (!is_array($mixed)) {
            $mixed = [$mixed];
        }

        $av = new FileTextValue();
        foreach ($mixed as $index => $text) {
            $page = new FilePageTextValue();
            $page->setAttributeValue($av);
            $page->setPage($index);
            $page->setText($text);
            $av->getPages()->add($page);
        }

        return $av;
    }
}