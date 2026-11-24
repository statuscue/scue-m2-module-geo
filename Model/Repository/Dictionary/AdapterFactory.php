<?php

namespace Scue\Geo\Model\Repository\Dictionary;

use Magento\Framework\ObjectManager\TMapFactory;
use Scue\Geo\Api\DictionaryRepository\AdapterInterface;

class AdapterFactory
{
    private $adapters;

    public function __construct(TMapFactory $mapFactory, array $adapters = [])
    {
        $this->adapters = $mapFactory->create([
            'array' => $adapters,
            'type' => AdapterInterface::class
        ]);

        foreach ($this->adapters as $key => $adapter) {
            if (! $adapter->isEnabled()) {
                unset($this->adapters[$key]);
            }
        }

        $this->adapters = $this->adapters->getIterator();
        $this->adapters->uasort(fn ($x, $y) => $x->getSortOrder() <=> $y->getSortOrder());
    }

    public function generate()
    {
        foreach ($this->adapters as $adapter) {
            yield $adapter;
        }
    }
}