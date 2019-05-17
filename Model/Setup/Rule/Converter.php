<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * Rule Converter.
 *
 * @category  Smile
 * @package   Smile\RetailerAdmin
 * @author    Tony DEPLANQUE <tony.deplanque@smile.fr>
 * @copyright 2019 Smile
 * @license   OSL-3.0
 */
namespace Smile\RetailerAdmin\Model\Setup\Rule;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Setup\SampleData\FixtureManager;

/**
 * Class Converter
 *
 * @package Smile\RetailerAdmin\Model\Setup\Rule
 */
class Converter
{
    /**
     * Fixture Manager.
     *
     * @var FixtureManager
     */
    protected $fixtureManager;

    /**
     * File Csv Framework.
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @var File
     */
    protected $filesystem;

    /**
     * Converter constructor.
     *
     * @param SampleDataContext $sampleDataContext Sample data context.
     * @param File              $filesystem        Filesystem.
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        File $filesystem
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->filesystem = $filesystem;
    }

    /**
     * Get Rule Resources.
     *
     * @param string $path File path.
     *
     * @return array
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getResources($path) : array
    {
        return $this->getFileContent($path);
    }

    /**
     * Convert content, fetch file content.
     *
     * @param string $path Path.
     *
     * @return array
     *
     * @throws \Exception
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getFileContent($path) : array
    {
        $filename = null;
        $resources = [];
        if (strpos($path, \Magento\Framework\View\Asset\Repository::FILE_ID_SEPARATOR)) {
            $filename = $this->fixtureManager->getFixture($path);
        }

        if ($filename && $this->filesystem->isExists($filename)) {
            $resources = array_column($this->csvReader->getData($filename), 0);
        }

        return $resources;
    }
}
