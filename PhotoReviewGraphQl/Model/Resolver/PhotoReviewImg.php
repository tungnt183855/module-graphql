<?php

declare(strict_types=1);

namespace Magenest\PhotoReviewGraphQl\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class PhotoReviewImg implements ResolverInterface
{
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection,
        \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail\CollectionFactory $reviewDetailCollection,
        \Magenest\PhotoReview\Model\ResourceModel\Photo\CollectionFactory $photoCollection
    ) {
        $this->productCollection = $productCollection;
        $this->reviewCollection = $reviewCollection;
        $this->reviewDetailCollection = $reviewDetailCollection;
        $this->photoCollection = $photoCollection;
    }

    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            if(isset($value['photo_review_is_recommend']) && !empty($value['photo_review_is_recommend'])) {
                $photo = $this->photoCollection->create()->addFieldToFilter('review_id', $value['review_id'])->getData();
                return $photo;
            }

            return [];

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
                ['*']
            );
        return $reviewCollection;
    }
}
