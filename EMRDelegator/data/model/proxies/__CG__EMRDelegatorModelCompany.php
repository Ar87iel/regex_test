<?php

namespace Model\Proxies\__CG__\EMRDelegator\Model;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Company extends \EMRDelegator\Model\Company implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'cluster', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'companyId', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'createdAt', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'lastModified', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'name', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'onlineStatus', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'facilities', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'migrationStatus');
        }

        return array('__isInitialized__', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'cluster', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'companyId', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'createdAt', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'lastModified', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'name', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'onlineStatus', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'facilities', '' . "\0" . 'EMRDelegator\\Model\\Company' . "\0" . 'migrationStatus');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Company $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function addFacility(\EMRDelegator\Model\Facility $facility)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addFacility', array($facility));

        return parent::addFacility($facility);
    }

    /**
     * {@inheritDoc}
     */
    public function removeFacility(\EMRDelegator\Model\Facility $facility)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeFacility', array($facility));

        return parent::removeFacility($facility);
    }

    /**
     * {@inheritDoc}
     */
    public function getFacilities()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getFacilities', array());

        return parent::getFacilities();
    }

    /**
     * {@inheritDoc}
     */
    public function setCluster($cluster)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCluster', array($cluster));

        return parent::setCluster($cluster);
    }

    /**
     * {@inheritDoc}
     */
    public function getCluster()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCluster', array());

        return parent::getCluster();
    }

    /**
     * {@inheritDoc}
     */
    public function setCompanyId($companyId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCompanyId', array($companyId));

        return parent::setCompanyId($companyId);
    }

    /**
     * {@inheritDoc}
     */
    public function getCompanyId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getCompanyId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCompanyId', array());

        return parent::getCompanyId();
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt($createdAt)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCreatedAt', array($createdAt));

        return parent::setCreatedAt($createdAt);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedAt()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCreatedAt', array());

        return parent::getCreatedAt();
    }

    /**
     * {@inheritDoc}
     */
    public function setLastModified($lastModified)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLastModified', array($lastModified));

        return parent::setLastModified($lastModified);
    }

    /**
     * {@inheritDoc}
     */
    public function getLastModified()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLastModified', array());

        return parent::getLastModified();
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', array($name));

        return parent::setName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getName', array());

        return parent::getName();
    }

    /**
     * {@inheritDoc}
     */
    public function setOnlineStatus($onlineStatus)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setOnlineStatus', array($onlineStatus));

        return parent::setOnlineStatus($onlineStatus);
    }

    /**
     * {@inheritDoc}
     */
    public function getOnlineStatus()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getOnlineStatus', array());

        return parent::getOnlineStatus();
    }

    /**
     * {@inheritDoc}
     */
    public function filterFacilities($criteria)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'filterFacilities', array($criteria));

        return parent::filterFacilities($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function isOnlineStatusAll()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isOnlineStatusAll', array());

        return parent::isOnlineStatusAll();
    }

    /**
     * {@inheritDoc}
     */
    public function isOnlineStatusNone()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isOnlineStatusNone', array());

        return parent::isOnlineStatusNone();
    }

    /**
     * {@inheritDoc}
     */
    public function isOnlineStatusSystem()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isOnlineStatusSystem', array());

        return parent::isOnlineStatusSystem();
    }

    /**
     * {@inheritDoc}
     */
    public function isOnlineStatusSuperUser()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isOnlineStatusSuperUser', array());

        return parent::isOnlineStatusSuperUser();
    }

    /**
     * {@inheritDoc}
     */
    public function setMigrationStatus($migrationStatus)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMigrationStatus', array($migrationStatus));

        return parent::setMigrationStatus($migrationStatus);
    }

    /**
     * {@inheritDoc}
     */
    public function getMigrationStatus()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMigrationStatus', array());

        return parent::getMigrationStatus();
    }

    /**
     * {@inheritDoc}
     */
    public function isReadyForMigration()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isReadyForMigration', array());

        return parent::isReadyForMigration();
    }

}
