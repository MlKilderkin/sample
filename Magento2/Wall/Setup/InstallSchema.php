<?php

namespace Mn\Wall\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
	/**
	 * install tables
	 *
	 * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
	 * @param \Magento\Framework\Setup\ModuleContextInterface $context
	 * @return void
	 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
	 */
	public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();
		if (!$installer->tableExists('mn_wall_post')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('mn_wall_post')
			)
				->addColumn(
					'wall_id',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					null,
					[
						'identity' => true,
						'nullable' => false,
						'primary'  => true,
						'unsigned' => true,
					],
					'Wall ID'
				)
				->addColumn(
					'name',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					['nullable => false'],
					'Wall Name'
				)
				->addColumn(
					'url_key',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					[],
					'Wall URL Key'
				)
				->addColumn(
					'wall_content',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					'64k',
					[],
					'Wall Post Content'
				)
				->addColumn(
					'tags',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					[],
					'Wall Tags'
				)
				->addColumn(
					'status',
					\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
					1,
					[],
					'Wall Status'
				)
				->addColumn(
					'featured_image',
					\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
					255,
					[],
					'Wall Featured Image'
				)

				->addColumn(
					'created_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					[],
					'Post Created At'
				)
				->addColumn(
					'updated_at',
					\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
					null,
					[],
					'Post Updated At'
				)
				->setComment('Post Table');
			$installer->getConnection()->createTable($table);

			$installer->getConnection()->addIndex(
				$installer->getTable('mn_wall_post'),
				$setup->getIdxName(
					$installer->getTable('mn_wall_post'),
					['name','url_key','wall_content','tags','featured_image'],
					\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
				),
				['name','url_key','wall_content','tags','featured_image'],
				\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
			);
		}
		$installer->endSetup();
	}
}