<?php
namespace Mageplaza\GiftCard\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
        $installer = $setup;
        $installer->startSetup();

        if(version_compare($context->getVersion(), '2.0.0', '<')) {

            if (!$installer->tableExists('giftcard_code')) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('giftcard_code')
                )
                    ->addColumn(
                        'giftcard_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'nullable' => false,
                            'primary'  => true,
                            'unsigned' => true,
                        ],
                        'Giftcard Id'
                    )
                    ->addColumn(
                        'code',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['nullable => false'],
                        'Code'
                    )
                    ->addColumn(
                        'balance',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '12,4',
                        [],
                        'Balance'
                    )
                    ->addColumn(
                        'amount_used',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '12,4',
                        [],
                        'Amount Used'
                    )
                    ->addColumn(
                        'create_from',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['nullable => false'],
                        'Create Form'
                    )
                    ->addColumn(
                        'created_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                        'Created At'
                    )
                    ->setComment('Giftcard Code');
                $installer->getConnection()->createTable($table);
            }

            if (!$installer->tableExists('giftcard_history')) {
                $table = $installer->getConnection()->newTable(
                    $installer->getTable('giftcard_history')
                )
                    ->addColumn(
                        'history_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'nullable' => false,
                            'primary'  => true,
                            'unsigned' => true,
                        ],
                        'History Id'
                    )
                    ->addColumn(
                        'giftcard_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                         'nullable'=>true,
                         'unsigned'=>true
                        ],
                        'Gift Card Id'
                    )
                    ->addColumn(
                        'customer_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'nullable'=>true,
                            'unsigned'=>true
                        ],
                        'Customer Id'
                    )
                    ->addColumn(
                        'amount',
                        \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        '12,4',
                        [],
                        'Amount'
                    )
                    ->addColumn(
                        'action',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        null,
                        [],
                        'Action'
                    )
                    ->addColumn(
                        'action_time',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                        'Created At'
                    )
                    ->addForeignKey(
                        $setup->getFkName('giftcard_history', 'giftcard_id', ' giftcard_code', 'giftcard_id'),
                        'giftcard_id',
                        $setup->getTable('giftcard_code'),
                        'giftcard_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )
                    ->addForeignKey(
                        $setup->getFkName('giftcard_history', 'customer_id', ' customer_entity', 'entity_id'),
                        'customer_id',
                        $setup->getTable('customer_entity'),
                        'entity_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )

                    ->setComment('Gift Card History');
                $installer->getConnection()->createTable($table);
            }




        }

        $installer->endSetup();
    }
}