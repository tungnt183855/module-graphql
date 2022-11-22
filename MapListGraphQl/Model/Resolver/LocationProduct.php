<?php

declare(strict_types=1);

namespace Magenest\MapListGraphQl\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magenest\MapList\Model\ResourceModel\LocationProduct\CollectionFactory;

class LocationProduct implements ResolverInterface
{
    public function __construct(
        CollectionFactory $locationProductCollection
    ) {
        $this->locationProductCollection = $locationProductCollection;
    }

    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            if(isset($value['location_id']) && !empty($value['location_id'])) {
                return $this->locationProductCollection->create()->addFieldToFilter('location_id', $value['location_id'])->getData();
            }
            return [];
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
    }
}
