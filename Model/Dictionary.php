<?php

namespace Scue\Geo\Model;

use Magento\Framework\DataObject;
use Scue\Geo\Api\Data\Dictionary as DictionaryInterface;

class Dictionary extends DataObject implements DictionaryInterface
{
    public function getName(): ?string
    {
        return $this->getData($this->getData('name_key'));
    }

    public function getId(): string|int
    {
        return (string)$this->getData($this->getData('id_key'));
    }

    public function getArea(): ?string
    {
        return (string)$this->getData($this->getData('area_key'));
    }
}