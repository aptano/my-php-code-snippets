<?php

namespace ProjVh\Checkout\Model;

use Magento\Checkout\Model\Session as CheckoutSession;

class Session extends CheckoutSession
{

    /**
     * Load data for customer quote and merge with current quote
     *
     * @return $this
     */
    public function loadCustomerQuote()
    {
        if (!$this->_customerSession->getCustomerId()) {
            return $this;
        }

        $this->_eventManager->dispatch('load_customer_quote_before', ['checkout_session' => $this]);

        try {
            $customerQuote = $this->quoteRepository->getForCustomer($this->_customerSession->getCustomerId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $customerQuote = $this->quoteFactory->create();
        }
        $customerQuote->setStoreId($this->_storeManager->getStore()->getId());

        // missmatch between current loaded quote id and customer Quote
        // unset quote id to load correct quote by customer id
        $this->clearQuote();

        $this->_quote = $this->getQuote();
        // $this->quoteRepository->save($this->_quote);

        $this->getQuote()->getBillingAddress();
        $this->getQuote()->getShippingAddress();
        $this->getQuote()->setCustomer($this->_customerSession->getCustomerDataObject())
            ->setTotalsCollectedFlag(false)
            ->collectTotals();
        $this->quoteRepository->save($this->getQuote());

        return $this;
    }

}
