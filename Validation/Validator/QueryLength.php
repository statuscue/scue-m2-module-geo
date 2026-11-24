<?php

namespace Scue\Geo\Validation\Validator;

use Magento\Framework\Message\MessageInterface;
use Magento\Framework\Validator\AbstractValidator;

class QueryLength extends AbstractValidator
{
    const MIN_QUERY_LENGTH = 2;

    public function isValid($value)
    {
        if (mb_strlen($value) < $this->getMinQueryLength()) {
            $this->_addMessages([
                MessageInterface::TYPE_ERROR => [
                    __('Query length must be at least %1 symbols', $this->getMinQueryLength())
                ],
                MessageInterface::TYPE_NOTICE => [
                    'Validation failed'
                ]
            ]);
            return false;
        }

        return true;
    }

    protected function getMinQueryLength(): int
    {
        return self::MIN_QUERY_LENGTH;
    }
}