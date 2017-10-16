<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Search\Adapter\Mysql\Field;

/**
 * Interface \Magento\Framework\Search\Adapter\Mysql\Field\ResolverInterface
 *
 */
interface ResolverInterface
{
    /**
     * Resolve field
     *
     * @param array $fields
     * @return FieldInterface[]
     */
    public function resolve(array $fields);
}
