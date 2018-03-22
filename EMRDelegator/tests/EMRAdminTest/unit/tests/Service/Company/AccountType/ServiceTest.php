<?php
namespace EMRAdminTest\unit\tests\Service\Company\AccountType;

use EMRCore\Config\Service\PrivateService\Esb\Dto\Route;
use EMRCore\EsbFactory;
use EMRCore\Marshaller\DtoToArray;
use EMRCore\Zend\Http\ClientWrapper;
use EMRCore\Zend\Http\Request\Pagination\Dto\Pagination;
use EMRCore\Zend\Http\Request\Pagination\Marshaller\DtoToArray as PaginationDtoToArray;
use EMRCore\Zend\module\Service\src\Response\Parser\Json;
use EMRCoreTest\lib\PhpUnit\SingletonTestCaseHelper;
use EMRModel\Company\CompanyAccountType;
use PHPUnit_Framework_TestCase;
use Zend\Http\Response;
use EMRAdmin\Service\Company\AccountType\Service as CompanyAccountTypeService;
use EMRAdmin\Service\Company\AccountType\Dao\Doctrine;
use stdClass;

/**
 *
 * @category WebPT
 * @package EMRAdmin
 * @copyright Copyright (c) 2014 WebPT, INC
 */
class ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CompanyAccountTypeService
     */
    protected $sut;

    /**
     * @var Doctrine|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dao;

    public function setUp()
    {
        $this->sut = new CompanyAccountTypeService();
        $this->dao = $this->createMock('EMRAdmin\Service\Company\AccountType\Dao\Doctrine');
        $this->sut->setAccountTypeDao($this->dao);
    }

    public function testGetList()
    {
        $this->dao->expects($this->once())
            ->method('getList')
            ->willReturn($expected = new stdClass());

        $result = $this->sut->getList();

        $this->assertSame($expected, $result);
    }
    public function testGetById()
    {
        $id = 200;

        $this->dao->expects($this->once())
            ->method('load')
            ->with($id)
            ->willReturn($expected = new stdClass());

        $result = $this->sut->getById($id);

        $this->assertSame($expected, $result);
    }

    public function testCreate()
    {
        $companyAccountType = new CompanyAccountType();

        $this->dao->expects($this->once())
            ->method('create')
            ->with($companyAccountType)
            ->willReturn($companyAccountType);

        $result = $this->sut->create($companyAccountType);

        $this->assertSame($companyAccountType, $result);
    }

    public function testUpdate()
    {
        $id = 100;

        $companyAccountType = new CompanyAccountType();

        $companyAccountType->setId($id);


        $this->dao->expects($this->once())
            ->method('load')
            ->with($id)
            ->willReturn($companyAccountType);

        $this->dao->expects($this->once())
            ->method('update')
            ->with($companyAccountType)
            ->willReturn($companyAccountType);

        $result = $this->sut->update($companyAccountType);

        $this->assertSame($companyAccountType, $result);
    }

    public function testDelete()
    {
        $companyAccountType = new CompanyAccountType();

        $this->dao->expects($this->once())
            ->method('delete')
            ->with($companyAccountType)
            ->willReturn($companyAccountType);

        $result = $this->sut->delete($companyAccountType);

        $this->assertSame($companyAccountType, $result);
    }
}