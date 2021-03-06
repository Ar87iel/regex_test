<?php

namespace Model\Proxies\__CG__\EMRDelegator\Model;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class Cluster extends \EMRDelegator\Model\Cluster implements \Doctrine\ORM\Proxy\Proxy
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
            return array('__isInitialized__', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'acceptingNewCompanies', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'clusterId', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'comment', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'createdAt', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'currentFacilityCount', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'lastModified', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'maxFacilityCount', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'name', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'onlineStatus', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'companies');
        }

        return array('__isInitialized__', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'acceptingNewCompanies', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'clusterId', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'comment', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'createdAt', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'currentFacilityCount', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'lastModified', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'maxFacilityCount', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'name', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'onlineStatus', '' . "\0" . 'EMRDelegator\\Model\\Cluster' . "\0" . 'companies');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (Cluster $proxy) {
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
    public function setAcceptingNewCompanies($acceptingNewCompanies)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAcceptingNewCompanies', array($acceptingNewCompanies));

        return parent::setAcceptingNewCompanies($acceptingNewCompanies);
    }

    /**
     * {@inheritDoc}
     */
    public function getAcceptingNewCompanies()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAcceptingNewCompanies', array());

        return parent::getAcceptingNewCompanies();
    }

    /**
     * {@inheritDoc}
     */
    public function setClusterId($clusterId)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setClusterId', array($clusterId));

        return parent::setClusterId($clusterId);
    }

    /**
     * {@inheritDoc}
     */
    public function getClusterId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getClusterId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getClusterId', array());

        return parent::getClusterId();
    }

    /**
     * {@inheritDoc}
     */
    public function setComment($comment)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setComment', array($comment));

        return parent::setComment($comment);
    }

    /**
     * {@inheritDoc}
     */
    public function getComment()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getComment', array());

        return parent::getComment();
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
    public function setCurrentFacilityCount($currentFacilityCount)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCurrentFacilityCount', array($currentFacilityCount));

        return parent::setCurrentFacilityCount($currentFacilityCount);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrentFacilityCount()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCurrentFacilityCount', array());

        return parent::getCurrentFacilityCount();
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
    public function setMaxFacilityCount($maxFacilityCount)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setMaxFacilityCount', array($maxFacilityCount));

        return parent::setMaxFacilityCount($maxFacilityCount);
    }

    /**
     * {@inheritDoc}
     */
    public function getMaxFacilityCount()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getMaxFacilityCount', array());

        return parent::getMaxFacilityCount();
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
    public function addFacility($count = 1)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addFacility', array($count));

        return parent::addFacility($count);
    }

    /**
     * {@inheritDoc}
     */
    public function removeFacility($count = 1)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeFacility', array($count));

        return parent::removeFacility($count);
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
    public function setCompanies(\Doctrine\Common\Collections\ArrayCollection $companies)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCompanies', array($companies));

        return parent::setCompanies($companies);
    }

    /**
     * {@inheritDoc}
     */
    public function getCompanies()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCompanies', array());

        return parent::getCompanies();
    }

    /**
     * {@inheritDoc}
     */
    public function addCompany(\EMRDelegator\Model\Company $company)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'addCompany', array($company));

        return parent::addCompany($company);
    }

    /**
     * {@inheritDoc}
     */
    public function removeCompany(\EMRDelegator\Model\Company $company)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'removeCompany', array($company));

        return parent::removeCompany($company);
    }

}
