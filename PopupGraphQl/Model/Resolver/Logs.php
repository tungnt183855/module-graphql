<?php

declare(strict_types=1);

namespace Magenest\PopupGraphQl\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magenest\Popup\Model\ResourceModel\Log\CollectionFactory;

class Logs implements ResolverInterface
{
    public function __construct(
        CollectionFactory $logCollection
    ) {
        $this->logCollection = $logCollection;
    }

    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            if(isset($args['log_id']) && !empty($args['log_id'])) {
                return $this->logCollection->create()->addFieldToFilter('log_id', $args['log_id']);
            }
            $data = $this->logCollection->create()->getData();
            return $data ? $data : [];
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
    }
}
