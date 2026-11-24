<?php

namespace Scue\Geo\Service;

use Throwable;
use InvalidArgumentException;
use Scue\Geo\Exceptions\OutOfCountry;
use Scue\Geo\Api\CityFetchService;
use Scue\Geo\Api\CityFetchService\Strategy;
use Scue\Geo\Model\Dictionary\Language;
use Magento\Framework\Locale\Resolver;
use Scue\Geo\Service\CityFetch\DTOFactory;

class CityFetch implements CityFetchService
{
    public function __construct(
        private Resolver $localeResolver,
        private CityFetch\StrategyFactory $strategyFactory,
        private DTOFactory $DTOFactory,
        private array $strategies = []
    ) {
    }

    public function getCity()
    {
        $language = $this->getLanguage();

        /**
         * @var Strategy $strategy
         */
        foreach ($this->strategyFactory->generate($this->strategies) as $strategy) {
            if (! $strategy->isEnabled()) {
                continue;
            }

            try {
                return $strategy->getDTO($language);
            } catch (OutOfCountry $e) {
                $response = $e->getResponse();
                $response = current($response);

                if (!$response) {
                    continue;
                }

                return $this->DTOFactory->createDTO([
                    'koatuu' => null,
                    'name' => $response['City']
                ]);
            } catch (Throwable) {
            }
        }

        try {
            return $this->strategyFactory->get('config')->getDTO($language);
        } catch (Throwable) {
            throw new InvalidArgumentException('Invalid json config value in "Stores / Configuration / Scue / Geodata / Default data for Ukrainian|Default data for Russian"');
        }
    }

    public function getLanguage(): Language
    {
        return Language::fromLocale($this->localeResolver->getLocale());
    }
}