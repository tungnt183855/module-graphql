<?php

declare(strict_types=1);

namespace Magenest\PopupGraphQl\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magenest\Popup\Model\ResourceModel\Template\CollectionFactory;

class Templates implements ResolverInterface
{
    public function __construct(
        CollectionFactory $templateCollection
    ) {
        $this->templateCollection = $templateCollection;
    }

    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        try {
            if(isset($args['template_id']) && !empty($args['template_id'])) {
                return $this->templateCollection->create()->addFieldToFilter('template_id', $args['template_id']);
            }
            $data = $this->templateCollection->create()->getData();
            return $data ? $data : [];
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
    }
}
