<?php

namespace Scue\Geo\Api;

use Scue\Geo\Api\Data\CityFetchService\Strategy\DTO;

interface CityFetchService
{
    /**
     * @return DTO
     */
    public function getCity();
}