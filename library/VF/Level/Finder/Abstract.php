<?php
/**
 * Vehicle Fits
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@vehiclefits.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Vehicle Fits to newer
 * versions in the future. If you wish to customize Vehicle Fits for your
 * needs please refer to http://www.vehiclefits.com for more information.
 * @copyright  Copyright (c) 2013 Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Level_Finder_Abstract implements VF_Configurable
{
    /** @var VF_Level_IdentityMap */
    protected $identityMap;

    /** @var Zend_Config */
    protected $config;

    protected $schema;

    function __construct($schema = null)
    {
        $this->schema = $schema ? $schema : new VF_Schema;
    }

    protected function getSchema()
    {
        return $this->schema;
    }

    function getConfig()
    {
        if (!$this->config instanceof Zend_Config) {

            $this->config = VF_Singleton::getInstance()->getConfig();
        }
        return $this->config;
    }

    function setConfig(Zend_Config $config)
    {
        $this->config = $config;
    }

    function identityMap()
    {
        if (is_null($this->identityMap)) {
            $this->identityMap = new VF_Level_IdentityMap;
        }
        return $this->identityMap;
    }

    /** @return Zend_Db_Statement_Interface */
    function query($sql)
    {
        return $this->getReadAdapter()->query($sql);
    }

    /** @return Zend_Db_Adapter_Abstract */
    protected function getReadAdapter()
    {
        return VF_Singleton::getInstance()->getReadAdapter();
    }

    function getTable($table)
    {
        return 'elite_level_' . $this->getSchema()->id() . '_' . str_replace(' ', '_', $table);
    }
}