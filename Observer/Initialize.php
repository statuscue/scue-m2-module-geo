<?php

namespace Scue\Geo\Observer;

use Scue\Geo\Exceptions\OutOfCountry;
use Throwable;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Scue\Geo\Service\CityFetch\StrategyFactory;
use Scue\Geo\Api\CityFetchService;
use Magento\Store\Model\StoreManagerInterface;
use Scue\Geodata\Helper\Data;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Scue\Geo\Service\CityFetch\Strategy\Cookie\Cookies;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Scue\Geo\Model\Dictionary\Language;

class Initialize implements ObserverInterface
{
    public function __construct(
        private StrategyFactory $strategyFactory,
        private CityFetchService $cityFetchService,
        private StoreManagerInterface $storeManager,
        private CookieManagerInterface $cookieManager,
        private Data $dataHelper,
        private RemoteAddress $remoteAddress
    ) {
    }

    public function execute(Observer $observer)
    {
        $cookieData = $this->strategyFactory->get('cookie');

        $currentStoreCode = $this->storeManager->getStore()->getCode();
        $cookieStoreCode = $this->cookieManager->getCookie(Cookies::STORE->value);

        $selectedCity = $this->cookieManager->getCookie(Cookies::SELECTED_CITY->value);

        if ($cookieData->isEnabled() && $currentStoreCode == $cookieStoreCode && $selectedCity) {
            return;
        }

        $language = Language::tryFrom($this->storeManager->getStore()->getCode());

        if (! $language) {
            $language = $this->cityFetchService->getLanguage();
        }

        if ($id = $cookieData->getDTO($language)->getKoatuu()) {
            try {
                if ($city = $this->dataHelper->getCityByKOATUUCode($id, $language->getLocale())) {
                    $cookies = [
                        Cookies::SELECTED_CITY_NAME->value => $city
                    ];
                }
            } catch (Throwable $e) {
            }
        }

        if (! isset($cookies)) {
            $city = $this->cityFetchService->getCity();
            $cookies = [
                Cookies::SELECTED_CITY_NAME->value => $city->getName(),
                Cookies::SELECTED_CITY->value => $city->getKoatuu(),
                'geodatadebug_ip' => $this->remoteAddress->getRemoteAddress()
            ];

            try {
                $cookies['geodatadebug_response'] = $this->strategyFactory->get('ip')->getResponse($language);
            } catch (OutOfCountry $exception) {
                $cookies['geodatadebug_response'] = $exception->getResponse();
            } catch (Throwable $throwable) {
                $cookies['geodatadebug_response'] = $throwable->getMessage();
            }

            if (is_array($cookies['geodatadebug_response'])) {
                $cookies['geodatadebug_response'] = json_encode($cookies['geodatadebug_response']);
            }
        }

        if (! isset($cookies[Cookies::STORE->value])) {
            $cookies[Cookies::STORE->value] = $currentStoreCode;
        }

        foreach ($cookies as $name => $value) {
            $this->setCookie($name, $value);
        }
        
    }

    private function setCookie(string $name, mixed $value, int $duration = null): void
    {
        if ($duration === null) {
            $duration = 60 * 60 * 24 * 365; // one year
        }

        $this->dataHelper->setCookie($name, $value, $duration);
    }
}