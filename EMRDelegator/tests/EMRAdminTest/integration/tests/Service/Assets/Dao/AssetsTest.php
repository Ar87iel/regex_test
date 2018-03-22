<?php

namespace EMRAdminTest\integration\tests\Service\Assets\Dao;

use PHPUnit_Framework_TestCase;
use InvalidArgumentException;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;
use Application\Controller\Form\Assets\GetFacilityAsset;
use EMRAdmin\Service\Assets\Dao\Assets;
use EMRAdmin\Service\Assets\Dto\GetFacilityAssetRequest;
use EMRCore\Service\Assets\Facility;
use EMRCore\Service\Assets\Dto\FacilityPathRequest;
use EMRCoreTest\Helper\Reflection;
use EMRAdmin\Service\Assets\Dto\GetUserAssetRequest;
use EMRCore\File\File;

/**
 *
 *
 * @category WebPT
 * @package EMRAdmin
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class AssetsTest extends PHPUnit_Framework_TestCase
{

    /**
     *
     * @var ServiceLocatorInterface 
     */
    private $serviceLocator;

    /**
     *
     * @var string 
     */
    private $base;

    /**
     *
     * @var string 
     */
    private $filename;

    /**
     *
     * @var string 
     */
    private $filename2;

    public function setUp()
    {
        $this->serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        // Base folder for test files.
        $this->base = sys_get_temp_dir() . '/' . md5(__METHOD__) . 'base/';



        // Files inside directories.
        $this->filename = "{$this->base}image.jpg";

        // Files inside directories.
        $this->filename2 = "{$this->base}image2.jpg";


        @mkdir($this->base, 0766, true);

        //image string
        $data = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
                . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
                . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
                . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';

        $data = base64_decode($data);

        $this->saveTemporalAsset($this->filename, $data);
    }

    public function tearDown()
    {
        @unlink($this->filename);
        @unlink($this->filename2);
        @rmdir($this->base);
    }

    /**
     * test an image is resized to specific size
     */
    public function testResizeUserAsset()
    {
        $assetDao = new Assets();
        $assetDao->setServiceLocator($this->serviceLocator);

        //size to test is 200px according to UI agreement       
        $resizedImage = Reflection::invoke($assetDao, 'resizeUserAsset', array($this->filename, 200));

        $this->saveTemporalAsset($this->filename2, $resizedImage);

        $arrImageSize = getimagesize($this->filename2);

        $this->assertEquals(200, $arrImageSize[0]);
        $this->assertEquals(128, $arrImageSize[1]);
    }

    /**
     * 
     * @param string $filename
     * @param string $asset
     */
    private function saveTemporalAsset($filename, $asset)
    {
        $handle = fopen($filename, 'w+');

        fwrite($handle, $asset);

        fclose($handle);
    }

}