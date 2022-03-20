<?php

namespace Concrete\Package\MdFileTextExtractor;

use Concrete\Core\Attribute\Category\CategoryService;
use Concrete\Core\Attribute\Category\FileCategory;
use Concrete\Core\Attribute\TypeFactory;
use Concrete\Core\Entity\Attribute\Key\FileKey;
use Concrete\Core\File\Type\TypeList as FileTypeList;
use Concrete\Core\File\Type\Type as FileType;
use Concrete\Core\Package\Package;

class Controller extends Package
{
    /**
     * The minimum concrete5 version compatible with this package.
     *
     * @var string
     */
    protected $appVersionRequired = '8.5.5';

    /**
     * The handle of this package.
     *
     * @var string
     */
    protected $pkgHandle = 'md_file_text_extractor';

    /**
     * The version number of this package.
     *
     * @var string
     */
    protected $pkgVersion = '0.0.1';

    /**
     * @see https://documentation.concretecms.org/developers/packages/adding-custom-code-to-packages
     *
     * @var string[]
     */
    protected $pkgAutoloaderRegistries = [
        'src/Entity' => '\Macareux\MdFileTextExtractor\Entity',
    ];

    /**
     * Get the translated name of the package.
     *
     * @return string
     */
    public function getPackageName()
    {
        return t('File Text Extractor');
    }

    /**
     * Get the translated package description.
     *
     * @return string
     */
    public function getPackageDescription()
    {
        return t('Extract text from pdf files uploaded to the file manager.');
    }

    /**
     * Install process of the package.
     */
    public function install()
    {
        $this->registerAutoload();

        if (!class_exists('\Smalot\PdfParser\Parser')) {
            throw new \Exception('Run composer install before install this package.');
        }

        $pkg = parent::install();

        /** @var TypeFactory $factory */
        $factory = $this->app->make(TypeFactory::class);
        $type = $factory->getByHandle('file_text');
        if (!is_object($type)) {
            $type = $factory->add('file_text', 'File Text', $pkg);
            /** @var CategoryService $service */
            $service = $this->app->make(CategoryService::class);
            /** @var FileCategory $fileCategory */
            $fileCategory = $service->getByHandle('file')->getController();
            $fileCategory->associateAttributeKeyType($type);

            $key = $fileCategory->getAttributeKeyByHandle('md_file_text');
            if (!is_object($key)) {
                $key = new FileKey();
                $key->setAttributeKeyHandle('md_file_text');
                $key->setAttributeKeyName('File Text');
                $key->setIsAttributeKeyContentIndexed(true);
                $fileCategory->add('file_text', $key, null, $pkg);
            }
        }
    }

    public function on_start()
    {
        $this->registerAutoload();

        $fileTypeList = FileTypeList::getInstance();
        $fileTypeList->define('pdf', t('PDF'), FileType::T_DOCUMENT, 'pdf', false, false, 'md_file_text_extractor');
    }

    /**
     * Register autoloader.
     */
    protected function registerAutoload()
    {
        if (file_exists($this->getPackagePath() . '/vendor/autoload.php')) {
            require $this->getPackagePath() . '/vendor/autoload.php';
        }
    }
}