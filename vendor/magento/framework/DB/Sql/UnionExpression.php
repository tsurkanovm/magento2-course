<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\DB\Sql;

use Magento\Framework\DB\Select;

/**
 * Class UnionExpression
 */
class UnionExpression extends Expression
{
    /**
     * @var Select[]
     */
    protected $parts;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param Select[] $parts
     * @param string $type
     */
    public function __construct(array $parts, $type = Select::SQL_UNION)
    {
        $this->parts = $parts;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $parts = [];
        foreach ($this->parts as $part) {
            if ($part instanceof Select) {
                $parts[] = sprintf('(%s)', $part->assemble());
            } else {
                $parts[] = $part;
            }
        }
        return implode($parts, $this->type);
    }
}
