<?php

namespace Scue\Geo\Api\CityFetchService;

use Scue\Geo\Api\Data\CityFetchService\Strategy\DTO;
use Scue\Geo\Model\Dictionary\Language;

interface Strategy
{
    public function getDTO(Language $lang): DTO;
    public function getSortOrder(): int;
    public function isEnabled(): bool;
}