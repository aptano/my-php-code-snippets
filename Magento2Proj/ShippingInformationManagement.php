<?php
namespace ProjVh\Checkout\Plugin\Checkout\Model;


use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Quote\Model\QuoteRepository;
use Voith\Checkout\Helper\Data as CheckoutHelper;

class ShippingInformationManagement
{
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * @var CheckoutHelper
     */
    private $checkoutHelper;

    /**
     * ShippingInformationManagement constructor.
     * @param QuoteRepository $quoteRepository
     * @param CheckoutHelper $checkoutHelper
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        CheckoutHelper $checkoutHelper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->checkoutHelper = $checkoutHelper;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        $quote = $this->quoteRepository->getActive($cartId);

        if ($extAttributes) {
            $quote->setDeliveryDate($extAttributes->getDeliveryDate());
            $quote->setCustomerReference($extAttributes->getCustomerReference());

            $orderComment = $extAttributes->getOrderComment();
            if ($extAttributes->getFasterDelivery()) {
                $orderComment .= "\n".$this->checkoutHelper->getFasterDeliverCommentText();
            }
            $quote->setOrderComment($orderComment);
            $quote->setMarkingLoop($extAttributes->getMarkingLoop());
        }
    }
}