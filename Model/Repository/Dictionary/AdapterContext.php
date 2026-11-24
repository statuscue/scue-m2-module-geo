<?php

namespace Scue\Geo\Model\Repository\Dictionary;

use Magento\Framework\ObjectManagerInterface;

class AdapterContext
{
    public function __construct(public readonly ObjectManagerInterface $objectManager)
    {
    }
}