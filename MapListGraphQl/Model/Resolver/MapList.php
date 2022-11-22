<?php

declare(strict_types=1);

namespace Magenest\MapListGraphQl\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magenest\MapList\Model\ResourceModel\Location\CollectionFactory;

class MapList implements ResolverInterface
{
    public function __construct(
        CollectionFactory $locationCollection
    ) {
        $this->locationCollection = $locationCollection;
    }

    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            if(isset($args['location_id']) && !empty($args['location_id'])) {
                if(isset($args['website']) && !empty($args['website'])) {
                    return $this->locationCollection->create()
                                ->addFieldToFilter('location_id', $args['location_id'])
                                ->addFieldToFilter('website', $args['website'])
                                ->getData();
                }
                return $this->locationCollection->create()->addFieldToFilter('location_id', $args['location_id'])->getData();
            } elseif (isset($args['website']) && !empty($args['website'])) {
                return $this->locationCollection->create()->addFieldToFilter('website', $args['website'])->getData();
            } else {
                throw new GraphQlInputException(__('Params location_id/website should be specified'));
            }

        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
    }
}
