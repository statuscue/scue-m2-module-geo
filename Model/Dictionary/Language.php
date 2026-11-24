<?php

namespace Scue\Geo\Model\Dictionary;

enum Language: string
{
    case UK = 'uk';
    case RU = 'ru';

    public function getLocale(): string
    {
        return match ($this) {
            self::RU => 'ru_RU',
            default => 'uk_UA'
        };
    }

    public static function fromLocale(string $locale): Language
    {
        return match($locale) {
            'ru_RU' => Language::RU,
            default => Language::UK
        };
    }
}