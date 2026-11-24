<?php

namespace Scue\Geo\Model\Repository\Dictionary\Adapter;

use Scue\Geo\Model\Repository\Dictionary\AdapterContext;
use Scue\Geo\Model\Repository\Dictionary\Adapter;
use Scue\Geo\Model\Dictionary\Source;
use Scue\Geodata\Api\GeodataInterface;
use Scue\Geo\Model\Dictionary\Language;

class Geodata extends Adapter
{
    const SORT_ORDER = 100;
    const ID_KEY = 'id';
    const NAME_KEY = 'text';

    public function __construct(private GeodataInterface $geodata, AdapterContext $context)
    {
        parent::__construct($context);
    }

    public function isEnabled(): bool
    {
        return $this->geodata->getState();
    }

    public function getSource(): Source
    {
        return Source::KOATUU;
    }

    public function getCities(string $q, Language $language, int $limit = 10): array
    {
        if ($cities = $this->geodata->getCities(['Region', 'Area'])) {
            $cities = json_decode($cities, true);

            return array_map(function($city) {
                $areaKey = $this->getAreaKey();

                $city[$areaKey] = [$city['Area'], $city['Region']];
                $city[$areaKey] = array_filter($city[$areaKey]);
                $city[$areaKey] = implode(', ', $city[$areaKey]);
                return $this->createDictionary($city);
            }, $cities);
        }

        return [];
    }
}