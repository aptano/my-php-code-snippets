<?php

namespace ProjVh\CustomerRoles\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;
use Voith\CustomerRoles\Model\DebitorRolesRepository;

class CustomerSaveAfterObserver implements ObserverInterface
{
    /**
     * Request
     *
     * @var RequestInterface
     */
    protected $_request;

    /**
     * customer
     *
     * @var \Magento\Customer\Model\CustomerFactory ,
     */
    protected $_customerFactory;

    /**
     * Debitor roles repository
     *
     * @var \Voith\Debitor\Model\DebitorRolesRepository
     */
    protected $debitorRolesRepository;

    /**
     * CustomerSaveAfterObserver constructor.
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request,
        DebitorRolesRepository $debitorRolesRepository
    ) {
        $this->_request = $request;
        $this->debitorRolesRepository = $debitorRolesRepository;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $customerId = (int)$customer->getId();

        $request = $observer->getEvent()->getRequest();
        $paramRoles = $request->getPost('roles');

        if (!empty($paramRoles)) {
            foreach ($paramRoles as $debitorId => $roles) {
                foreach ($roles as $roleId => $checked) {
                    $role = $this->debitorRolesRepository->getDebitorRoleByParams($customerId, $debitorId, $roleId);

                    if ($checked == '1') {
                        if (empty($role)) {
                            $this->debitorRolesRepository->insertCustomerDebitorRole($customerId, $debitorId, $roleId);
                        }
                    }

                    if ($checked == '0') {
                        if (!empty($role)) {
                            $this->debitorRolesRepository->deleteCustomerDebitorRole($role->getId());
                        }
                    }
                }
            }
        }

        return true;
    }
}