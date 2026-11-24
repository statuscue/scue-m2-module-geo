<?php

namespace Scue\Geo\Service\CityFetch;

use InvalidArgumentException;
use Magento\Framework\ObjectManager\TMapFactory;
use Scue\Geo\Api\CityFetchService\Strategy;

class StrategyFactory
{
    private $strategies;

    public function __construct(
        TMapFactory $mapFactory,
        array $strategies = []
    ) {
        $this->strategies = $mapFactory->create([
            'array' => $strategies,
            'type' => Strategy::class
        ]);

        $this->strategies = $this->strategies->getIterator();
        $this->strategies->uasort(fn ($x, $y) => $x->getSortOrder() <=> $y->getSortOrder());
    }

    /**
     * @yield Strategy
     */
    public function generate(array $available = [])
    {
        foreach ($this->strategies as $name => $strategy) {
            if (! empty($available) && ! in_array($name, $available)) {
                continue;
            }

            yield $strategy;
        }
    }

    public function get(string $name): Strategy
    {
        if (! isset($this->strategies[$name])) {
            throw new InvalidArgumentException("Strategy {$name} does not exist");
        }

        return $this->strategies[$name];
    }
}