<?php

namespace Scue\Geo\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\UrlInterface;
use Scue\Geo\Service\CityFetch;
use Scue\Geodata\Helper\Data;

class CitySwitcher implements ArgumentInterface
{
    const DEFAULT_CITIES = [
        8000000000,
        5110100000,
        2310100000,
        1210100000,
        1211000000,
        4610100000
    ];
    const DEFAULT_CITY = 5110100000;

    public function __construct(
        private CityFetch $service,
        private UrlInterface $urlBuilder,
        private Data $data
    ) {
    }

    public function getCurrentCityName()
    {
        return $this->service->getCity()->getName();
    }

    public function getTopCities()
    {
        $cities = [];

        foreach (self::DEFAULT_CITIES as $city) {
            $cities[$city] = $this->data->getCityByKOATUUCode($city, $this->service->getLanguage()->getLocale());
        }

        return $cities;
    }

    public function getDefaultTopCity()
    {
        return self::DEFAULT_CITY;
    }

    public function getSearchUrl()
    {
        return $this->urlBuilder->getUrl(null, [
            '_direct' => 'rest/V1/geo/city'
        ]);
    }
}