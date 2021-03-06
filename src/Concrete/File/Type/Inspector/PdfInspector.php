<?php

namespace Concrete\Package\MdFileTextExtractor\File\Type\Inspector;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Type\Inspector\Inspector;
use Concrete\Core\Package\PackageService;
use Macareux\MdFileTextExtractor\Entity\Attribute\Value\Value\FilePageTextValue;
use Macareux\MdFileTextExtractor\Entity\Attribute\Value\Value\FileTextValue;
use Smalot\PdfParser\Parser;
use voku\helper\UTF8;

class PdfInspector extends Inspector
{
    protected $shouldRemoveWhiteSpaces;

    public function __construct(PackageService $packageService)
    {
        $package = $packageService->getClass('md_file_text_extractor');
        $this->shouldRemoveWhiteSpaces = $package->getFileConfig()->get('inspector.remove_whitespace', false);
    }

    /**
     * This method is called when a File\Version class refreshes its attributes.
     * This can be used to update the File\Version attributes as well as its contents.
     *
     * @param \Concrete\Core\Entity\File\Version $fv
     * @throws \Exception
     */
    public function inspect(Version $fv)
    {
        $resource = $fv->getFileResource();

        $attributeValue = new FileTextValue();
        $parser = new Parser();
        try {
            $pdf = $parser->parseContent($resource->read());
            $pages = $pdf->getPages();
            foreach ($pages as $page) {
                $pageValue = new FilePageTextValue();
                $pageValue->setAttributeValue($attributeValue);
                $pageValue->setPage($page->getPageNumber());
                $pageValue->setText($this->cleanup($page->getText()));
                $attributeValue->getPages()->add($pageValue);
            }

            $attributeCategory = $fv->getObjectAttributeCategory();
            $attributeKey = $attributeCategory->getAttributeKeyByHandle('md_file_text');
            if ($attributeKey) {
                $fv->setAttribute($attributeKey, $attributeValue);
            }
        } catch (\Exception $exception) {

        }
    }

    public function cleanup(string $text)
    {
        $text = UTF8::cleanup($text);
        if ($this->shouldRemoveWhiteSpaces) {
            $text = preg_replace('/[\s\n\r]+/', '', $text);
        }

        return $text;
    }
}