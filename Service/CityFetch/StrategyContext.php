<?php

namespace Scue\Geo\Service\CityFetch;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class StrategyContext
{
    public function __construct(
        public readonly ObjectManagerInterface $objectManager,
        public readonly ScopeConfigInterface $scopeConfig,
        public readonly StoreManagerInterface $storeManager,
        public readonly DTOFactory $DTOFactory
    ) {
    }
}