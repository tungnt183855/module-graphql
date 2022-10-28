<?php

declare(strict_types=1);

namespace Magenest\PopupGraphQl\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magenest\Popup\Model\ResourceModel\Popup\CollectionFactory;

class Popups implements ResolverInterface
{
    public function __construct(
        CollectionFactory $popupCollection
    ) {
        $this->popupCollection = $popupCollection;
    }

    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            if(isset($args['popup_id']) && !empty($args['popup_id'])) {
                return $this->popupCollection->create()->addFieldToFilter('popup_id', $args['popup_id']);
            }
            $data = $this->popupCollection->create()->getData();
            return $data ? $data : [];
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
    }
}
