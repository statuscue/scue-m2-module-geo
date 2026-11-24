<?php

namespace Scue\Geo\Api\DictionaryRepository;

use Scue\Geo\Model\Dictionary\Source;
use Scue\Geo\Model\Dictionary\Language;
use Scue\Geo\Api\Data\Dictionary;

interface AdapterInterface
{
    public function getSource(): Source;
    public function isEnabled(): bool;
    public function getSortOrder(): int;

    /**
     * @param string $q
     * @param int $limit
     * @return Dictionary[]
     */
    public function getCities(string $q, Language $language, int $limit = 10): array;
}