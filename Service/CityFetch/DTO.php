<?php

namespace Scue\Geo\Service\CityFetch;

use Magento\Framework\DataObject;
use Scue\Geo\Api\Data\CityFetchService\Strategy\DTO as IDTO;

class DTO extends DataObject implements IDTO
{
    public function getKoatuu()
    {
        return $this->getData('koatuu');
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getStore()
    {
        return $this->getData('store');
    }
}