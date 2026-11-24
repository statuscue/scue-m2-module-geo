<?php

namespace Scue\Geo\Model\Repository;

use Magento\Framework\Validator\Factory as ValidatorFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Locale\ResolverInterface;
use Scue\Novaposhta\Model\ResourceModel\Settlements\CollectionFactory;

class DictionaryContext
{
    public function __construct(
        public readonly ValidatorFactory $validatorFactory,
        public readonly Request $request,
        public readonly Dictionary\AdapterFactory $adapterFactory
    ) {
    }
}