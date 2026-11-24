<?php

namespace Scue\Geo\Model\Repository\Dictionary;

use Scue\Geo\Api\Data\Dictionary;
use Scue\Geo\Api\DictionaryRepository\AdapterInterface;

abstract class Adapter implements AdapterInterface
{
    const SORT_ORDER = 0;
    const NAME_KEY = null;
    const ID_KEY = null;
    const AREA_KEY = 'region_description';

    public function __construct(protected readonly AdapterContext $context)
    {
    }

    public function getSortOrder(): int
    {
        return static::SORT_ORDER;
    }

    protected function getNameKey(): string
    {
        return static::NAME_KEY;
    }

    protected function getIdKey(): string
    {
        return static::ID_KEY;
    }

    protected function getAreaKey(): string
    {
        return static::AREA_KEY;
    }

    protected function createDictionary(array $data): Dictionary
    {
        $data['name_key'] = $this->getNameKey();
        $data['id_key'] = $this->getIdKey();
        $data['area_key'] = $this->getAreaKey();

        return $this->context->objectManager->create(Dictionary::class, compact('data'));
    }
}