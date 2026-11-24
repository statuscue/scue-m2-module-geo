<?php

namespace Scue\Geo\Service\CityFetch\Strategy;

use Scue\Geo\Api\Data\CityFetchService\Strategy\DTO;
use Scue\Geo\Service\CityFetch\Strategy;
use Scue\Geo\Service\CityFetch\StrategyContext;
use Scue\Geo\Model\Dictionary\Language;

class Config extends Strategy
{
    public const SORT_ORDER = 30;

    public function __construct(
        StrategyContext $strategyContext
    ) {
        parent::__construct($strategyContext);
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function getDTO(Language $lang): DTO
    {
        $xmlPath = 'global_geodata_params/geodata/default_response_';

        if ($lang->value == 'uk') {
            $xmlPath .= 'ua';
        } else {
            $xmlPath .= $lang->value;
        }

        $config = $this->context->scopeConfig->getValue($xmlPath);
        $config = json_decode($config, true);
        $config = current($config);

        return $this->createDTO([
            'koatuu' => $config['KOATUU'],
            'name' => $config['City']
        ]);
    }
}