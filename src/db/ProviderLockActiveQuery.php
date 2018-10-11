<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/patron/license
 * @link       https://www.flipboxfactory.com/software/patron/
 */

namespace flipbox\patron\db;

use craft\db\QueryAbortedException;
use flipbox\ember\db\traits\AuditAttributes;
use flipbox\ember\db\traits\FixedOrderBy;
use flipbox\patron\records\ProviderLock;
use yii\db\ActiveQuery;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ProviderLockActiveQuery extends ActiveQuery
{
    use FixedOrderBy,
        AuditAttributes;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->from === null) {
            $this->from = [ProviderLock::tableName() . ' ' . ProviderLock::tableAlias()];
        }
    }

    /**
     * @inheritdoc
     */
    public $orderBy = ['dateCreated' => SORT_DESC];

    /*******************************************
     * FIXED ORDER
     *******************************************/

    /**
     * @inheritdoc
     */
    protected function fixedOrderColumn(): string
    {
        return 'pluginId';
    }

    /*******************************************
     * PREPARE
     *******************************************/

    /**
     * @inheritdoc
     *
     * @throws QueryAbortedException if it can be determined that there won’t be any results
     */
    public function prepare($builder)
    {
        // Is the query already doomed?
        if (($this->plugin !== null && empty($this->plugin)) ||
            ($this->provider !== null && empty($this->provider))
        ) {
            throw new QueryAbortedException();
        }

        $this->applyAuditAttributeConditions();
        $this->applyOrderByParams($builder->db);

        return parent::prepare($builder);
    }
}
