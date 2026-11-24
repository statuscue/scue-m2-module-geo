<?php

namespace Scue\Geo\Api\Data\CityFetchService\Strategy;

interface DTO
{
    /**
     * @return int|string
     */
    public function getKoatuu();

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @return string
     */
    public function getStore();
}