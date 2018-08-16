<?php
namespace ProjVh\CustomerRoles\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Voith\CustomerRoles\Api\RolesRepositoryInterface;
use Voith\CustomerRoles\Exception\NoRolesFoundException;
use Voith\CustomerRoles\Model\ResourceModel\Roles\Collection;
use Voith\CustomerRoles\Model\ResourceModel\Roles\CollectionFactory;

/**
 * Class RolesRepository
 * @package Voith\CustomerRoles\Model
 */
class RolesRepository implements RolesRepositoryInterface
{
    /**
     * @var Roles[]
     */
    protected $instances = [];
    /**
     * @var RolesFactory
     */
    protected $rolesFactory;
    /**
     * @var CollectionFactory
     */
    private $rolesCollectionFactory;

    /**
     * @param RolesFactory    $rolesFactory
     * @param CollectionFactory $rolesCollectionFactory
     */
    public function __construct(
        RolesFactory $rolesFactory,
        CollectionFactory $rolesCollectionFactory
    ) {
        $this->rolesFactory = $rolesFactory;
        $this->rolesCollectionFactory = $rolesCollectionFactory;
    }

    /**
     * @param int $roleId roleId parameter
     *
     * @return Role
     * @throws NoSuchEntityException
     */
    public function get($roleId)
    {
        if (!isset($this->instances[$roleId])) {
            /**
             * @var Role $role
             */
            $role = $this->rolesFactory->create();
            $role->getResource()->load($role, $roleId);
            if (!$role->getId()) {
                throw NoSuchEntityException::singleField('id', $roleId);
            }
            $this->instances[$roleId] = $role;
        }

        return $this->instances[$roleId];
    }

    /**
     * @return Role[]
     * @throws NoRolesFoundException
     */
    public function getAllRoles()
    {
        /**
         * @var Collection $roleCollection
         */
        $roleCollection = $this->rolesCollectionFactory->create();

        $roles = $roleCollection->selectAll();

        if (!$roles) {
            throw new NoRolesFoundException();
        }

        return $roles;
    }
}
