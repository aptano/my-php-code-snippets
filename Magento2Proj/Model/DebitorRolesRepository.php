<?php
namespace ProjVh\CustomerRoles\Model;

use Voith\CustomerRoles\Api\DebitorRolesRepositoryInterface;
use Voith\CustomerRoles\Model\ResourceModel\DebitorRoles\Collection;
use Voith\CustomerRoles\Model\ResourceModel\DebitorRoles\CollectionFactory;

/**
 * Class DebitorRolesRepository
 * @package Voith\CustomerRoles\Model
 */
class DebitorRolesRepository implements DebitorRolesRepositoryInterface
{
    /**
     * @var DebitorRolesFactory
     */
    protected $debitorRolesFactory;

    /**
     * @var CollectionFactory
     */
    private $debitorRolesCollectionFactory;

    /**
     * @var array DebitorRoles[]
     */
    protected $instancesByCustomerId = [];

    /**
     * @var array DebitorRoles[]
     */
    protected $instancesRoles = [];

    /**
     * @var array DebitorRoles[]
     */
    protected $instancesRightsByCustomerId = [];

    /**
     * @param DebitorRolesFactory    $debitorRolesFactory
     * @param CollectionFactory      $debitorRolesCollectionFactory
     */
    public function __construct(
        DebitorRolesFactory $debitorRolesFactory,
        CollectionFactory $debitorRolesCollectionFactory
    ) {
        $this->debitorRolesFactory = $debitorRolesFactory;
        $this->debitorRolesCollectionFactory = $debitorRolesCollectionFactory;
    }

    /**
     * Get debitor roles by customerid
     *
     * @param int $customerId customerId
     * @return DebitorRoles[]
     */
    public function getDebitorRolesByCustomerId($customerId)
    {
        if (array_key_exists($customerId, $this->instancesByCustomerId)) {
            return $this->instancesByCustomerId[$customerId];
        }

        /**
         * @var Collection $rollesCollection
         */
        $rollesCollection = $this->debitorRolesCollectionFactory->create();
        $rollesCollection->addFieldToFilter('customer_id', $customerId);

        $debitorRoles = [];
        foreach ($rollesCollection as $debitorObject) {
            $debitorRoles[] = $debitorObject;
        }

        return $this->instancesByCustomerId[$customerId] = $debitorRoles;
    }

    /**
     * Get roles by customer id and debitor id
     *
     * @param int $customerId customerId
     * @param int $debitorId debitorId
     * @return DebitorRoles[]
     */
    public function getRolesByCustomerIdAndDebitorId($customerId, $debitorId)
    {
        if (array_key_exists($customerId, $this->instancesRoles)) {
            return $this->instancesRoles[$customerId];
        }

        /**
         * @var Collection $rollesCollection
         */
        $rollesCollection = $this->debitorRolesCollectionFactory->create();
        $rollesCollection->addFieldToFilter('customer_id', $customerId)
                         ->addFieldToFilter('debitor_id', $debitorId);

        $debitorRoles = [];
        foreach ($rollesCollection as $debitorObject) {
            $debitorRoles[] = $debitorObject;
        }

        return $this->instancesRoles[$customerId] = $debitorRoles;
    }

    /**
     * Get debitor role for customer
     *
     * @param int $customerId customerId
     * @param int $debitorId debitorId
     * @param int $roleId roleId
     * @return DebitorRoles[]
     */
    public function getDebitorRoleByParams($customerId, $debitorId, $roleId)
    {
        /**
         * @var Collection $rollesCollection
         */
        $rollesCollection = $this->debitorRolesCollectionFactory->create();
        $rollesCollection->addFieldToFilter('customer_id', $customerId)
                          ->addFieldToFilter('debitor_id', $debitorId)
                          ->addFieldToFilter('role_id', $roleId);

        $debitorRole = [];
        foreach ($rollesCollection as $debitorObject) {
            $debitorRole[] = $debitorObject;
        }

        if (!$debitorRole) {
            return false;
        }

        return $debitorRole[0];
    }

    /**
     * Insert role
     *
     * @param int $customerId customerId
     * @param int $debitorId debitorId
     * @param int $roleId roleId
     * @return boolean
     */
    public function insertCustomerDebitorRole($customerId, $debitorId, $roleId)
    {
        if (!empty($customerId) && !empty($debitorId) && !empty($roleId)) {
            $model = $this->debitorRolesFactory->create();
            $model->setData('customer_id', $customerId);
            $model->setData('debitor_id', $debitorId);
            $model->setData('role_id', $roleId);
            $model->save();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Delete role
     *
     * @param int $id id
     * @return boolean
     */
    public function deleteCustomerDebitorRole($id)
    {
        if (!empty($id)) {
            $model = $this->debitorRolesFactory->create();
            $model->load($id);
            $model->delete();
            return true;
        } else {
            return false;
        }
    }
}
