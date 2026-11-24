<?php

namespace Scue\Geo\Model\Repository;

use Throwable;
use Magento\Framework\Validator\Exception;
use Scue\Geo\Api\DictionaryRepositoryInterface;
use Scue\Geo\Model\LanguageDetectionSupport;
use Magento\Framework\Message\Notice;
use Magento\Framework\Message\MessageInterface;
use Scue\Geo\Api\DictionaryRepository\AdapterInterface;
use Scue\Geo\Model\Dictionary\Language;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as Collection;

class Dictionary implements DictionaryRepositoryInterface
{
    use LanguageDetectionSupport;

    const VALIDATION_ENTITY = 'scue_geo';

    public function __construct(private DictionaryContext $context)
    {
    }

    public function getCities()
    {
        $query = (string)$this->context->request->getParam('q');
        $language = $this->getLanguageByRequest($this->context->request);
        $errors = [];

        $this->validateQuery($query);

        foreach ($this->context->adapterFactory->generate() as $adapter) {
            try {
                $response = $this->getResponse($adapter, $query, $language);
            } catch (Throwable $exception) {
                $errors[] = $exception->getMessage() . ', in file: ' . $exception->getFile() . ', on line: ' . $exception->getLine();
                continue;
            }

            if (empty($response)) {
                break;
            }

            return $response;
        }

        if (empty($errors)) {
            $errors[] = __('No adapters were provided or they just return empty data');
        }

        $this->throwException(400, [__('Nothing was found')], $errors);
    }

    private function throwException(int $code, array $error, array $details = [])
    {
        $exception = new Exception(null, null, ['error' => $error], $code);
        array_walk($details, fn ($detail) => $exception->addMessage(new Notice($detail)));

        throw $exception;
    }

    private function validateQuery(string $query): void
    {
        $validator = $this->context->validatorFactory->createValidator(self::VALIDATION_ENTITY, 'request');

        if (! $validator->isValid($query)) {
            $messages = $validator->getMessages();
            $this->throwException(411, $messages[MessageInterface::TYPE_ERROR] ?? [], $messages[MessageInterface::TYPE_NOTICE] ?? []);
        }
    }

    private function getResponse(AdapterInterface $adapter, string $query, Language $language): array
    {
        if ($cities = $adapter->getCities($query, $language)) {
            return array_map(fn($city) => [
                'name' => $city->getName(),
                'id' => $city->getId(),
                'region' => $city->getArea()
            ], $cities);
        }

        return [];
    }
}