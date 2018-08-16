<?php
/**
 * Copyright © 2018 Voith. All rights reserved.
 */

namespace ProjVh\Checkout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_CSV_FILE_NAME = 'paymentgoal/csv_file/path';
    const XML_FASTER_DELIVERY_ENABLE = 'fasterdelivery/general/enable';
    const XML_FASTER_DELIVERY_CHECKBOX_LABEL = 'fasterdelivery/general/checkbox_label';
    const XML_FASTER_DELIVERY_TOOLTIP_TEXT = 'fasterdelivery/general/tooltip_text';
    const XML_FASTER_DELIVERY_COMMENT_TEXT = 'fasterdelivery/general/comment_text';
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $_fileCsv;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var \Voith\Debitor\Model\DebitorFactory
     */
    private $debitorFactory;
    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    protected $scopeConfig;

    /**
     * Data constructor.
     * @param Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Voith\Debitor\Model\DebitorFactory $debitorFactory
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\File\Csv $fileCsv
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Voith\Debitor\Model\DebitorFactory $debitorFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\File\Csv $fileCsv
    ) {
        parent::__construct($context);
        $this->scopeConfig = $context->getScopeConfig();

        $this->_fileCsv = $fileCsv;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;

        $this->debitorFactory = $debitorFactory;
        $this->directoryList = $directoryList;
    }

    /** Return Payment goal for matching store code and city_code.
     *
     * @return array
     */
    public function readForPaymentGoal()
    {
        $directory = $this->directoryList->getPath('var');
        $file = $directory .DIRECTORY_SEPARATOR.$this->getFilePath();
        try{
            $this->_fileCsv->setDelimiter(";");
            $data = $this->_fileCsv->getData($file);

            // Store code in website and in CSV file must be same for the stores other than english store.
            $storeCode = $this->storeManager->getStore()->getCode();
            if ($storeCode == 'default') {
                $storeCode = 'EN';
            }

            $cityCode = $this->getDebitorCityCode();
            $formattedValue = null;

            foreach ($data as $csvData) {
                if ((isset($cityCode['city_code']) && isset($csvData[0]) && $csvData[0] == $cityCode['city_code'])
                    && (isset($csvData[1]) && strtolower($csvData[1]) == strtolower($storeCode))
                ) {
                    $formattedValue = array_slice($csvData, 2);
                    $formattedValue = array_filter(array_map('trim', $formattedValue));
                    $formattedValue = preg_replace('/\s+/', ' ', $formattedValue);
                    break;
                }
            }
            return $formattedValue;
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
    }

    /** Get the file path for the csv file to look for the payment goal.
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->scopeConfig->getValue(
            self::XML_CSV_FILE_NAME
        );
    }

    /** Function to get citycode for the curent selected debitor.
     *
     * @return mixed
     */
    public function getDebitorCityCode()
    {
        $currentDebitorId = $this->customerSession->getDebitorId();
        $data = [];
        try {
            $debitor = $this->debitorFactory->create();
            $debitor->load($currentDebitorId);
            $debitorData = $this->customerSession->getDebitorData();
            foreach ($debitorData as $_debitorData) {
                if ($_debitorData->getKunnr() == $debitor->getNumber()) {

                    $data['city_code'] = $_debitorData->getCityCode();
                    break;
                }
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return $data;
    }

    public function isFasterDeliverEnable()
    {
        return $this->scopeConfig->getValue(
            self::XML_FASTER_DELIVERY_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getFasterDeliverLabel()
    {
        return $this->scopeConfig->getValue(
            self::XML_FASTER_DELIVERY_CHECKBOX_LABEL,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getFasterDeliverTooltipText()
    {
        return $this->scopeConfig->getValue(
            self::XML_FASTER_DELIVERY_TOOLTIP_TEXT,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getFasterDeliverCommentText()
    {
        return $this->scopeConfig->getValue(
            self::XML_FASTER_DELIVERY_COMMENT_TEXT,
            ScopeInterface::SCOPE_STORE
        );
    }
}