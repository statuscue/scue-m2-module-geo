<?php

namespace Scue\Geo\Service\CityFetch;

use Scue\Geo\Api\CityFetchService\Strategy as IStrategy;
use Scue\Geo\Api\Data\CityFetchService\Strategy\DTO as IDTO;

abstract class Strategy implements IStrategy
{
    const SORT_ORDER = 0;

    public function __construct(protected readonly StrategyContext $context)
    {
    }

    public function getSortOrder(): int
    {
        return static::SORT_ORDER;
    }

    public function createDTO(array $data): IDTO
    {
        return $this->context->DTOFactory->createDTO($data);
    }
}