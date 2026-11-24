<?php

namespace Scue\Geo\Service\CityFetch\Strategy;

use Scue\Geo\Service\CityFetch\Strategy;
use Scue\Geo\Api\Data\CityFetchService\Strategy\DTO;
use Scue\Geo\Service\CityFetch\StrategyContext;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Scue\Geo\Service\CityFetch\Strategy\Cookie\Cookies;
use Scue\Geo\Model\Dictionary\Language;

class Cookie extends Strategy
{
    public const SORT_ORDER = 10;

    public function __construct(private CookieManagerInterface $cookieManager, StrategyContext $strategyContext)
    {
        parent::__construct($strategyContext);
    }

    public function isEnabled(): bool
    {
        if ($this->context->scopeConfig->isSetFlag('global_geodata_params/geodata/handle_cookies')) {
            return (bool)($this->cookieManager->getCookie(Cookies::SELECTED_CITY_NAME->value) && $this->cookieManager->getCookie(Cookies::SELECTED_CITY->value));
        }

        return false;
    }

    public function getDTO(Language $lang): DTO
    {
        return $this->createDTO([
            'name' => $this->cookieManager->getCookie(Cookies::SELECTED_CITY_NAME->value),
            'koatuu' => $this->cookieManager->getCookie(Cookies::SELECTED_CITY->value)
        ]);
    }
}