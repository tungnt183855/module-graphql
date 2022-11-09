<?php

declare(strict_types=1);

namespace Magenest\PhotoReviewGraphQl\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class Reviews implements ResolverInterface
{
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection,
        \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail\CollectionFactory $reviewDetailCollection
    ) {
        $this->productCollection = $productCollection;
        $this->productRepository = $productRepository;
        $this->reviewCollection = $reviewCollection;
        $this->reviewDetailCollection = $reviewDetailCollection;
    }

    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            if(isset($args['product_id']) && !empty($args['product_id'])) {
                return $this->joinReviewAndReviewDetail($args['product_id'])->getData() ?: [];
            } elseif(isset($args['sku']) && !empty($args['sku'])) {
                $productId = $this->productRepository->get($args['sku'])->getEntityId();
                return $this->joinReviewAndReviewDetail($productId)->getData() ?: [];
            } else {
                throw new GraphQlInputException(__('Product id/SKU should be specified'));
            }

        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
    }

    private function joinReviewAndReviewDetail($productId) {
        $reviewCollection = $this->reviewCollection->create()->addFieldToFilter('entity_pk_value', $productId);
        $reviewCollection->getSelect()
            ->joinLeft(
                ['mpd' => $this->reviewDetailCollection->create()->getTable('magenest_photoreview_detail')],
                'main_table.review_id = mpd.review_id',
                [
                    'custom_id',
                    'photo_review_is_recommend',
                    'photo_review_pros',
                    'photo_review_cons',
                    'admin_comment',
                    'photo_external_links',
                    'is_purchased',
                    'rating_sum',
                    'order_id'
                ]
            );
        return $reviewCollection;
    }
}
