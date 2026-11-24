<?php

namespace Scue\Geo\Service\CityFetch\Strategy;

use Scue\Geo\Api\Data\CityFetchService\Strategy\DTO;
use Scue\Geo\Service\CityFetch\Strategy;
use Scue\Geo\Service\CityFetch\StrategyContext;
use Scue\Geodata\Api\GeodataInterface;
use Scue\Geodata\Helper\Data;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Scue\Geo\Model\Dictionary\Language;
use Scue\Geo\Exceptions\OutOfCountry;

class IP extends Strategy
{
    public const SORT_ORDER = 20;

    private $response;

    public function __construct(
        private GeodataInterface $geodata,
        private Data $data,
        private RemoteAddress $remoteAddress,
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
        if ($response = $this->getResponse($lang)) {
            $result = json_decode($response, true);

            if (! empty($result)) {
                $result = current($result);

                if (! empty($result['KOATUU'])) {
                    return $this->createDTO([
                        'koatuu' => $result['KOATUU'],
                        'name' => $result['City']
                    ]);
                }
            }
        }

        if (! is_array($response)) {
            $response = @json_decode($response, true);
        }

        throw new OutOfCountry('', 0, null, is_array($response) ? $response : []);
    }

    public function getResponse(Language $lang)
    {
        if ($this->response === null) {
            $bearerData = json_decode($this->geodata->auth(), true);
            $url = $this->context->scopeConfig->getValue('global_geodata_params/geodata/api_url') . 'api/CitiesByIp?sLang=' . $lang->getLocale() . '&ipaddress=' . $this->remoteAddress->getRemoteAddress();

            $this->response = $this->data->sendGet($url, $bearerData['access_token']);
        }

        return $this->response;
    }
}