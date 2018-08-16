<?php
namespace ProjVh\CustomerRoles\Model;

use Magento\Framework\Model\AbstractModel;
use Voith\CustomerRoles\Api\Data\DebitorRolesInterface;

/**
 * Class DebitorRoles
 * @package Voith\CustomerRoles\Model
 */
class DebitorRoles extends AbstractModel implements DebitorRolesInterface
{

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Voith\CustomerRoles\Model\ResourceModel\DebitorRoles');
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData('id');
    }

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getData('customer_id');
    }

    /**
     * Get debitor id
     *
     * @return int
     */
    public function getDebitorId()
    {
        return $this->getData('debitor_id');
    }

    /**
     * Get role id
     *
     * @return int
     */
    public function getRoleId()
    {
        return $this->getData('role_id');
    }
}
