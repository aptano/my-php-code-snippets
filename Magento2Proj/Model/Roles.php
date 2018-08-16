<?php
namespace ProjVh\CustomerRoles\Model;

use Magento\Framework\Model\AbstractModel;
use Voith\CustomerRoles\Api\Data\RolesInterface;

/**
 * Class Roles
 * @package Voith\CustomerRoles\Model
 */
class Roles extends AbstractModel implements RolesInterface
{

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Voith\CustomerRoles\Model\ResourceModel\Roles');
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData('role_id');
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }
}
