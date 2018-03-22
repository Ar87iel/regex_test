<?php
namespace EMRAdminTest\unit\tests\Service\WebOutcomes\Controller\External;

use PHPUnit_Framework_TestCase;
use Service\WebOutcomes\Controller\External\SubmodulesController;
use WebOutcomesModule\Submodules\Dto\SubmoduleCollection;
use WebOutcomesModule\Submodules\Dto\SubmoduleSaveRequest;
use Service\WebOutcomes\Controller\External\Form\SaveSubmodulesList;
use WebOutcomesModule\Submodules\Marshaller\SubmoduleCollectionToArray;
use WebOutcomesModule\Submodules\Marshaller\ArrayToSubmoduleSaveRequest;
use EMRCore\Zend\Form;
use Zend\Http\Request;

class SubmodulesControllerTest extends PHPUnit_Framework_TestCase
{
    /** @var  SubmodulesController */
    private $submoduleController;

    /** @var  Request */
    private $request;

    /** @var  SubmoduleCollectionToArray */
    private $submoduleCollection;

    /** @var  SaveSubmodulesList */
    private $saveSubmodule;

    /** @var  ArrayToSubmoduleSaveRequest */
    private $arraySubmoduleSaveRequest;

    /**
     * Initialize
     */
    public function setUp()
    {
        $this->submoduleController = 'Service\WebOutcomes\Controller\External';
        $this->request = 'Zend\Http\Request';
        $this->submoduleCollection = 'WebOutcomesModule\Submodules\Marshaller\SubmoduleCollectionToArray';
        $this->saveSubmodule = 'Service\WebOutcomes\Controller\External\Form\SaveSubmodulesList';
        $this->arraySubmoduleSaveRequest = 'WebOutcomesModule\Submodules\Marshaller\ArrayToSubmoduleSaveRequest';
    }

    /**
     * Test GetList Method
     */
    public function testGetList()
    {
        /** @var SubmodulesController $controller */
        $controller = $this->getMock($this->submoduleController, array('getSubmodulesListForm'));
        /** @var Request $request */
        $request = $this->getMock($this->request, array('getRequest'), array(), '', false);
        /** @var ServiceHelper $serviceHelper */
        $serviceHelper = $this->getMock('EMRCore\Zend\Form', array('validateFormData'));
        /** @var SubmoduleCollectionToArray $subCollection */
        $subCollection = $this->getMock($this->submoduleCollection, array('marshall'));

        $data = $request->getQuery();
        $form = $controller->getSubmodulesListForm();
        $serviceHelper->expects($this->any())->method('validateFormData')->with($form, $data->toArray());
        $list = new SubmoduleCollection;
        $marshalledList = array();
        $subCollection->expects($this->any())->method('marshall')->with($list)->will($this->returnValue($marshalledList));

        $this->assertInternalType('array', $marshalledList);
    }

    /**
     * Test Create Method
     */
    public function testCreate()
    {
        /** @var SubmodulesController $controller */
        $controller = $this->getMock($this->submoduleController, array('getSubmodulesListForm'));
        /** @var Request $request */
        $request = $this->getMock($this->request, array('getRequest'), array(), '', false);
        /** @var  $saveSumodules */
        $saveSumodules = $this->getMock($this->saveSubmodule, array('getSubmodulesSaveForm'));
        /** @var ServiceHelper $serviceHelper */
        $serviceHelper = $this->getMock('EMRCore\Zend\Form', array('validateFormData'));
        /** @var  $arraySaveRequest */
        $arraySaveRequest = $this->getMock($this->arraySubmoduleSaveRequest, array('marshall'));
        $saveRequestDto = new SubmoduleSaveRequest();
        $response = true;

        $controller->expects($this->any())->method('getSubmodulesSaveForm')->will($this->returnValue($saveSumodules));
        $data = $request->getQuery();
        $serviceHelper->expects($this->any())->method('validateFormData')->with($saveSumodules, $data->toArray());
        $arraySaveRequest->expects($this->any())->method('marshall')->with($data)
            ->will($this->returnValue($saveRequestDto));
        $submodules = $this->getMock('WebOutcomesModule\Submodules\Submodules', array('save'));
        $submodules->expects($this->any())->method('save')->with($saveRequestDto)
            ->will($this->returnValue($response));

        $content = array('success' => $response);

        $this->assertArrayHasKey('success', $content);
        $this->assertEquals(true, $content['success']);
    }

} 