<?php

declare(strict_types=1);

namespace Magenest\PhotoReviewGraphQl\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Product implements ResolverInterface
{
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection
    ) {
        $this->productCollection = $productCollection;
        $this->reviewCollection = $reviewCollection;
    }

    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            if(isset($value['entity_pk_value']) && !empty($value['entity_pk_value'])) {
                $product = $this->productCollection->create()->getItemById($value['entity_pk_value']);
                return $product->getData('sku');
            } else {
                throw new GraphQlInputException(__('Do not find any product'));
            }

        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
    }
}
