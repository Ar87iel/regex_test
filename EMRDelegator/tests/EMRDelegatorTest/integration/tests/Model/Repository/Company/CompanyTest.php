<?php
namespace EMRDelegatorTest\integration\tests\Model\Repository\Company;

use EMRCore\Zend\Http\Request\Pagination\Dto\Pagination;
use EMRCoreTest\Helper\Reflection;
use EMRDelegator\Model\Facility;
use EMRDelegator\Model\Repository\Company as CompanyRepository;
use EMRDelegator\Service\Company\Dto\SearchCompanyRequest;
use EMRDelegator\Service\Company\Dto\SearchCompanyResult;
use EMRDelegatorTest\Integration\Lib\DelegatorBaseTestCase;


class CompanyTest extends DelegatorBaseTestCase {

    protected $companies = array();
    protected $facilities = array();
    protected $companyPrefix = 'company_';
    protected $facilityPrefix = 'facility_';

    /**
     * @return CompanyRepository
     */
    protected static function getCompanyRepository() {
        return self::$defaultReaderWriter
            ->getEntityManager()
            ->getRepository('EMRDelegator\Model\Company');
    }

    public static function setUpBeforeClass() {
        parent::setUpBeforeClass();

        $stmt = self::$defaultReaderWriter->getEntityManager()
            ->getConnection()
            ->prepare(file_get_contents(dirname(dirname(dirname(dirname(__DIR__)))) . '/sql/Model/Repository/Company/setup_records.sql', true));
        $stmt->execute();
    }

    public function testSearchByCompanyIdIncludesAllCompanyFacilities()
    {
        $repository = $this->getCompanyRepository();
        $request = new SearchCompanyRequest;
        $request->setCompanyId(30);
        $resultsDto = $repository->searchCompany($request);
        /** @var $results SearchCompanyResult[] */
        $results = $resultsDto->getResults();
        $this->assertCount(1, $results);
        $this->assertEquals(3, count($results[0]->getFacilities()));
        return;
    }

    public function testSearchByCompanyNameIncludesAllCompanyFacilities()
    {
        $repository = $this->getCompanyRepository();
        $request = new SearchCompanyRequest;
        $request->setCompanyName('b');
        $resultsDto = $repository->searchCompany($request);
        /** @var $results SearchCompanyResult[] */
        $results = $resultsDto->getResults();
        $this->assertCount(1, $results);
        $this->assertEquals(2, $results[0]->getFacilities()->count());
        return;
    }

    public function testSearchByFacilityIdIncludesCompanyAndMatchingFacility()
    {
        $repository = $this->getCompanyRepository();
        $request = new SearchCompanyRequest;
        $request->setFacilityId(40);
        $resultsDto = $repository->searchCompany($request);
        $results = $resultsDto->getResults();

        $this->assertCount(1, $results);
        $this->assertEquals(1, $results[0]->getFacilities()->count());
        $this->assertEquals('c', $results[0]->getCompany()->getName());
        $this->assertEquals('ha', $results[0]->getFacilities()->first()->getName());
        return;
    }

    public function testSearchByFacilityNameIncludesCompanyAndMatchingFacility()
    {
        $repository = $this->getCompanyRepository();
        $request = new SearchCompanyRequest;
        $request->setFacilityName('a');
        $resultsDto = $repository->searchCompany($request);
        $results = $resultsDto->getResults();

        $this->assertCount(2, $results);
        $this->assertEquals('b', $results[0]->getCompany()->getName());
        $this->assertEquals(2, $results[0]->getFacilities()->count());
        $this->assertEquals('ga', $results[0]->getFacilities()->first()->getName());
        $this->assertEquals('gba',$results[0]->getFacilities()->offsetGet(1)->getName());

        $this->assertEquals('c', $results[1]->getCompany()->getName());
        $this->assertEquals(1, $results[1]->getFacilities()->count());
        $this->assertEquals('ha', $results[1]->getFacilities()->first()->getName());
        return;
    }

    public function testFindFacilitiesByCompanyIdsReturnsMatchingFacilitiesForProvidedCompanyIds() {
        $repository = $this->getCompanyRepository();
        /** @var $results Facility[] */
        $results = $repository->findFacilitiesByCompanyIds(array(20,30), null, 'a');
        $this->assertEquals(3, count($results));
        // match company 2, facility 2
        $this->assertEquals(20, $results[0]->getCompany()->getCompanyId());
        $this->assertEquals(20, $results[0]->getFacilityId());
        // match company 2, facility 3
        $this->assertEquals(20, $results[1]->getCompany()->getCompanyId());
        $this->assertEquals(30, $results[1]->getFacilityId());
        // match company 3, facility 4
        $this->assertEquals(30, $results[2]->getCompany()->getCompanyId());
        $this->assertEquals(40, $results[2]->getFacilityId());
    }

    public function testSearchByFacilityNamePaginatesCompanyNotFacility()
    {
        $repository = $this->getCompanyRepository();

        $pagination = new Pagination;
        $pagination->setPage(1);
        $pagination->setPerPage(10);

        $request = new SearchCompanyRequest;
        $request->setFacilityName($facilityName = 'a');
        $request->setPagination($pagination);

        $results = Reflection::invoke($repository, 'searchCompanyIdsWithPagination', array($request));

        $this->assertCount(2, $results);
        $this->assertSame(20, $results[0]);
        $this->assertSame(30, $results[1]);
    }

    public function testSearchByCompanyIdPaginatesCompanyNotFacility()
    {
        $repository = $this->getCompanyRepository();

        $pagination = new Pagination;
        $pagination->setPage(1);
        $pagination->setPerPage(10);

        $request = new SearchCompanyRequest;
        $request->setCompanyId($companyId = 30);
        $request->setPagination($pagination);

        $results = Reflection::invoke($repository, 'searchCompanyIdsWithPagination', array($request));

        $this->assertCount(1, $results);
        $this->assertSame($companyId, $results[0]);
    }
}