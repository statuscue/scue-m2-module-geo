<?php

namespace Scue\Geo\Model;

use Scue\Geo\Model\Dictionary\Language;
use Magento\Framework\App\RequestInterface;

trait LanguageDetectionSupport
{
    public function getLanguageByRequest(RequestInterface $request): Language
    {
        $lang = (string)$request->getParam('lang');

        if ($locale = Language::tryFrom($lang)) {
            return $locale;
        }

        return Language::UK;
    }
}