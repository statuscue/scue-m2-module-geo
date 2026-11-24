<?php

namespace Scue\Geo\Model\Repository\Dictionary\Adapter;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as Collection;
use Zend_Db_Select;
use Zend_Db_Expr;
use Scue\Geo\Model\Repository\Dictionary\AdapterContext;
use Scue\Geo\Model\Repository\Dictionary\Adapter;
use Scue\Geo\Model\Dictionary\Source;
use Scue\Geodata\Api\GeodataInterface;
use Scue\Novaposhta\Model\ResourceModel\Settlements\CollectionFactory;
use Scue\Geo\Model\Dictionary\Language;

class Novaposhta extends Adapter
{
    const SORT_ORDER = 110;
    const ID_KEY = 'index_coatsu1';

    private $nameKey;

    public function __construct(private CollectionFactory $collectionFactory, AdapterContext $context)
    {
        parent::__construct($context);
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function getSource(): Source
    {
        return Source::NOVAPOSHTA;
    }

    public function getCities(string $q, Language $language, int $limit = 10): array
    {
        $this->initNameKeyByLang($language);

        $cities = [];
        $citiesCollection = $this->getCitiesCollection($q, $limit);

        foreach ($citiesCollection as $city) {
            $areaKey = $this->getAreaKey();
            $data = $city->getData();

            if ($language == Language::RU) {
                $data[$areaKey] = [$data['area_description_ru'], $data['regions_description_ru']];
            } else {
                $data[$areaKey] = [$data['area_description'], $data['regions_description']];
            }

            $data[$areaKey] = array_filter($data[$areaKey]);
            $data[$areaKey] = implode(',', $data[$areaKey]);

            $cities[] = $this->createDictionary($data);
        }

        return $cities;
    }

    private function createCollection()
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToSelect([
            $this->getIdKey(),
            'description',
            'description_ru',
            'ref',
            'area_description',
            'area_description_ru',
            'regions_description',
            'regions_description_ru'
        ]);
        $columns = $collection->getSelect()->getPart(Zend_Db_Select::COLUMNS);
        $columns[] = [
            'main_table',
            new Zend_Db_Expr('10'),
            'order'
        ];
        $collection->getSelect()->setPart(Zend_Db_Select::COLUMNS, $columns);

        return $collection;
    }

    /**
     * @param string $q
     * @param $limit
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    private function getCitiesCollection(string $q, $limit)
    {
        $collection = $this->createCollection();
        $collection->addFieldToFilter(['description', 'description_ru'], [
            [
                'like' => $q . '%'
            ], [
                'like' => $q . '%'
            ]
        ]);

        $unionSelect = $this->createCollection();
        $unionSelect->addFieldToFilter(['description', 'description_ru'], [
            [
                'like' => '%' . $q . '%'
            ], [
                'like' => '%' . $q . '%'
            ]
        ]);

        $newest = clone $collection->getSelect()->union([(string)$collection->getSelect(), (string)$unionSelect->getSelect()]);

        $newest->reset('from');
        $collection->getSelect()->reset();
        $collection->getSelect()->from(['main_table' => new Zend_Db_Expr('(' . (string)$newest . ')')]);
        $collection->getSelect()->order('order');
        $collection->getSelect()->limit($limit);
        $collection->getselect()->group('ref');

        return $collection;
    }

    protected function getNameKey(): string
    {
        return $this->nameKey;
    }

    private function initNameKeyByLang(Language $language): void
    {
        $this->nameKey = match ($language) {
            Language::RU => 'description_ru',
            default => 'description'
        };
    }

    private function assignAdditionalData(array $cities, Language $language): array
    {
        if (! empty($cities)) {
            $columns = [
                'area' => 'area_description',
                'region' => 'regions_description'
            ];

            if ($language == Language::RU) {
                foreach ($columns as &$column) {
                    $column .= '_ru';
                }
            }

            $columns[] = 'index_coatsu1';

            /**
             * @var Collection $collection
             */
            $collection = $this->context->settlementCollectionFactory->create();
            $collection->addFieldToSelect($columns);
            $collection->addFieldToFilter('index_coatsu1', ['in' => array_column($cities, 'id')]);

            $cities = array_map(function($city) use ($collection) {
                if ($data = $collection->getItemByColumnValue('index_coatsu1', $city['id'])) {
                    $city['area'] = [$data->getArea(), $data->getRegion()];
                    $city['area'] = array_filter($city['area']);
                    $city['area'] = implode(', ', $city['area']);
                }

                return $city;
            }, $cities);
        }

        return $cities;
    }
}