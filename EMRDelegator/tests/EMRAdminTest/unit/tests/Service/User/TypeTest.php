<?php
namespace EMRAdminTest\unit\tests\Service\User;

use EMRAdmin\Service\User\Dto\Type as TypeDto;
use EMRAdmin\Service\User\Dto\TypeCollection;
use EMRAdmin\Service\User\Type;
use EMRModel\User\UserType;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

/**
 *
 *
 * @category WebPT
 * @package
 * @copyright Copyright (c) 2013 WebPT, INC
 */
class TypeTest extends PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $entityManager;
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $userTypeRepository;
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $masterAdapter;
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $prototypeFactory;

    /** @var  Type */
    private $sut;

    protected function setUp()
    {
        parent::setUp();

        $this->prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);
        $this->prototypeFactory->expects($this->any())->method('createAndInitialize')
            ->will($this->returnCallback(function($name) {
                if ($name === 'EMRAdmin\Service\User\Dto\TypeCollection')
                {
                    return new TypeCollection;
                }

                if ($name === 'EMRAdmin\Service\User\Dto\Type')
                {
                    return new TypeDto;
                }

                throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
            }));

        $this->entityManager = $this->getMock('\Doctrine\Common\Persistence\ObjectManager');
        $this->userTypeRepository = $this->getMock('\Doctrine\Common\Persistence\ObjectRepository');
        $this->entityManager->expects($this->any())->method('getRepository')
            ->will($this->returnValueMap(array(
                array('EMRModel\User\UserType', $this->userTypeRepository),
            )));

        $this->masterAdapter = $this->getMock('\EMRCore\DoctrineConnector\Adapter\Adapter');
        $this->masterAdapter->expects($this->any())->method('getEntityManager')
            ->will($this->returnValue($this->entityManager));

        $this->sut = new Type;
        $this->sut->setPrototypeFactory($this->prototypeFactory);
        $this->sut->setDefaultMasterSlave($this->masterAdapter);
    }

    public function testGetListHasNoDuplicates()
    {
        $this->userTypeRepository->expects($this->any())->method('findAll')
            ->willReturn(array(
                $userTypeA = new UserType(),
                $userTypeB = new UserType(),
            ));

        $userTypeA->setDescription('asdf');
        $userTypeB->setDescription('asdf');

        $collection = $this->sut->getList();

        $this->assertCount(2, $collection);

        $types = array_unique($collection->getDescriptions()->toArray());

        $this->assertCount(1, $types);
    }

    public function testGetsClinicianInsuranceListDueToIsTherapist()
    {
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $collection = $this->getMock('EMRAdmin\Service\User\Dto\TypeCollection');
        $collection->expects($this->once())->method('push');

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name) use ($collection)
                {

                    if ($name === 'EMRAdmin\Service\User\Dto\TypeCollection')
                    {
                        return $collection;
                    }

                    throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                }));

        // Mocking stdClass ensures that we only call the functions that are registered in our expectation.
        // We care just as much about ensuring that every other function is NOT called in this logic.
        $type = $this->getMock('stdClass', array('isTherapist'));
        $type->expects($this->once())
            ->method('isTherapist')
            ->will($this->returnValue(true));

        $service = $this->getMock('EMRAdmin\Service\User\Type', array('getList'));

        $service->expects($this->once())
            ->method('getList')
            ->will($this->returnValue(array($type)));

        /** @var Type $service */
        $service->setPrototypeFactory($prototypeFactory);
        $service->getClinicianList();
    }

    public function testGetsSupportListDueToIsSuperUser()
    {
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $collection = $this->getMock('EMRAdmin\Service\User\Dto\TypeCollection');
        $collection->expects($this->once())
            ->method('push');

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name) use ($collection)
                {

                    if ($name === 'EMRAdmin\Service\User\Dto\TypeCollection')
                    {
                        return $collection;
                    }

                    throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                }));

        // Mocking stdClass ensures that we only call the functions that are registered in our expectation.
        // We care just as much about ensuring that every other function is NOT called in this logic.
        $type = $this->getMock('stdClass', array('isSuperUser'));
        $type->expects($this->once())
            ->method('isSuperUser')
            ->will($this->returnValue(true));

        $service = $this->getMock('EMRAdmin\Service\User\Type', array('getList'));

        $service->expects($this->once())
            ->method('getList')
            ->will($this->returnValue(array($type)));

        /** @var Type $service */
        $service->setPrototypeFactory($prototypeFactory);
        $service->getSupportList();
    }

    public function testGetsClericalListDueToIsClerical()
    {
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $collection = $this->getMock('EMRAdmin\Service\User\Dto\TypeCollection');
        $collection->expects($this->exactly(2))->method('push');

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name) use ($collection)
                {

                    if ($name === 'EMRAdmin\Service\User\Dto\TypeCollection')
                    {
                        return $collection;
                    }

                    throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                }));

        // Mocking stdClass ensures that we only call the functions that are registered in our expectation.
        // We care just as much about ensuring that every other function is NOT called in this logic.
        $typeClerical = $this->getMock('stdClass', array('isClerical'));
        $typeClerical->expects($this->once())->method('isClerical')->will($this->returnValue(true));

        $typeBilling = $this->getMock('stdClass', array('isBilling', 'isClerical'));
        $typeBilling->expects($this->once())->method('isBilling')->will($this->returnValue(true));

        $service = $this->getMock('EMRAdmin\Service\User\Type', array('getList'));

        $service->expects($this->once())->method('getList')
            ->will($this->returnValue(array($typeClerical, $typeBilling)));

        /** @var Type $service */
        $service->setPrototypeFactory($prototypeFactory);
        $service->getClericalList();
    }

    public function testGetsAssistantStudentListDueToIsStudent()
    {
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $collection = $this->getMock('EMRAdmin\Service\User\Dto\TypeCollection');
        $collection->expects($this->once())
            ->method('push');

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(function($name) use ($collection) {

                if ($name === 'EMRAdmin\Service\User\Dto\TypeCollection')
                {
                    return $collection;
                }

                throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
            }));

        // Mocking stdClass ensures that we only call the functions that are registered in our expectation.
        // We care just as much about ensuring that every other function is NOT called in this logic.
        $type = $this->getMock('stdClass', array('isStudent'));
        $type->expects($this->once())
            ->method('isStudent')
            ->will($this->returnValue(true));

        $service = $this->getMock('EMRAdmin\Service\User\Type', array('getList'));

        $service->expects($this->once())
            ->method('getList')
            ->will($this->returnValue(array($type)));

        /** @var Type $service */
        $service->setPrototypeFactory($prototypeFactory);
        $service->getStudentList();
    }

    public function testGetsAssistantStudentListDueToIsAssistant()
    {
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $collection = $this->getMock('EMRAdmin\Service\User\Dto\TypeCollection');
        $collection->expects($this->once())
            ->method('push');

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name) use ($collection)
                {

                    if ($name === 'EMRAdmin\Service\User\Dto\TypeCollection')
                    {
                        return $collection;
                    }

                    throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                }));

        // Mocking stdClass ensures that we only call the functions that are registered in our expectation.
        // We care just as much about ensuring that every other function is NOT called in this logic.
        $type = $this->getMock('stdClass', array('isStudent', 'isAssistant'));
        $type->expects($this->once())
            ->method('isAssistant')
            ->will($this->returnValue(true));

        $service = $this->getMock('EMRAdmin\Service\User\Type', array('getList'));

        $service->expects($this->once())
            ->method('getList')
            ->will($this->returnValue(array($type)));

        /** @var Type $service */
        $service->setPrototypeFactory($prototypeFactory);
        $service->getAssistantList();
    }

    public function testGetsAssistantStudentListDueToIsRespiratoryTherapistTechnician()
    {
        $prototypeFactory = $this->getMock('EMRCore\PrototypeFactory', array(), array(), '', false);

        $collection = $this->getMock('EMRAdmin\Service\User\Dto\TypeCollection');
        $collection->expects($this->once())
            ->method('push');

        $prototypeFactory->expects($this->any())
            ->method('createAndInitialize')
            ->will($this->returnCallback(
                function($name) use ($collection)
                {

                    if ($name === 'EMRAdmin\Service\User\Dto\TypeCollection')
                    {
                        return $collection;
                    }

                    throw new InvalidArgumentException("Mocked PrototypeFactory cannot provide [$name].");
                }));

        // Mocking stdClass ensures that we only call the functions that are registered in our expectation.
        // We care just as much about ensuring that every other function is NOT called in this logic.
        $type = $this->getMock('stdClass', array('isStudent', 'isAssistant', 'isRespiratoryTherapistTechnician'));
        $type->expects($this->once())
            ->method('isAssistant')
            ->will($this->returnValue(false));
        $type->expects($this->once())
            ->method('isRespiratoryTherapistTechnician')
            ->will($this->returnValue(true));

        $service = $this->getMock('EMRAdmin\Service\User\Type', array('getList'));

        $service->expects($this->once())
            ->method('getList')
            ->will($this->returnValue(array($type)));

        /** @var Type $service */
        $service->setPrototypeFactory($prototypeFactory);
        $service->getAssistantList();
    }

    public function testGetAssistantStudentListMergesAssistantStudentCollections()
    {
        $userType1 = new TypeDto;
        $userType1->setDescription('asdf');

        $userType2 = new TypeDto;
        $userType2->setDescription('qwer');

        $collection1 = new TypeCollection;
        $collection1->push($userType1);

        $collection2 = new TypeCollection;
        $collection2->push($userType2);

        $service = $this->getMock('EMRAdmin\Service\User\Type', array('getAssistantList', 'getStudentList'));

        $service->expects($this->once())
            ->method('getAssistantList')
            ->will($this->returnValue($collection1));

        $service->expects($this->once())
            ->method('getStudentList')
            ->will($this->returnValue($collection2));

        /** @var Type $service */
        $collection = $service->getAssistantStudentList();

        $this->assertCount(2, $collection);
    }
}