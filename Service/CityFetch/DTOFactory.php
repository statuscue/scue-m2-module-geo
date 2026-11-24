<?php

namespace Scue\Geo\Service\CityFetch;

use Scue\Geo\Api\Data\CityFetchService\Strategy\DTO as IDTO;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class DTOFactory
{
    public function __construct(private StoreManagerInterface $storeManager, private ObjectManagerInterface $objectManager)
    {
    }

    public function createDTO(array $data): IDTO
    {
        if (! isset($data['store'])) {
            $data['store'] = $this->storeManager->getStore()->getCode();
        }

        return $this->objectManager->create(DTO::class, compact('data'));
    }
}