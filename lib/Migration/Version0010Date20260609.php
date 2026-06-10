<?php
/**
 * Nextcloud - TimeBox
 */

namespace OCA\TimeBox\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version0010Date20260609 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $closure
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $closure, array $options): void {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $closure
	 * @param array $options
	 * @return ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $closure, array $options): ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $closure();

		// Create timeboxes table
		if (!$schema->hasTable('timebox_timeboxes')) {
			$table = $schema->createTable('timebox_timeboxes');
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('user_id', Types::STRING, [
				'notnull' => true,
				'length' => 64,
			]);
			$table->addColumn('title', Types::STRING, [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn('color', Types::STRING, [
				'notnull' => false,
				'length' => 7,
				'default' => '#0082c9',
			]);
			$table->addColumn('start_time', Types::INTEGER, [
				'notnull' => false,
			]);
			$table->addColumn('end_time', Types::INTEGER, [
				'notnull' => false,
			]);
			$table->addColumn('created_time', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['id']);
			$table->addIndex(['user_id'], 'timebox_user_idx');
		}

		// Create items table
		if (!$schema->hasTable('timebox_items')) {
			$table = $schema->createTable('timebox_items');
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
			]);
			$table->addColumn('timebox_id', Types::INTEGER, [
				'notnull' => true,
			]);
			$table->addColumn('item_type', Types::STRING, [
				'notnull' => true,
				'length' => 20,
			]);
			$table->addColumn('item_source_id', Types::STRING, [
				'notnull' => false,
				'length' => 255,
			]);
			$table->addColumn('title', Types::STRING, [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn('description', Types::TEXT, [
				'notnull' => false,
			]);
			$table->addColumn('sort_order', Types::INTEGER, [
				'notnull' => false,
				'default' => 0,
			]);
			$table->addColumn('calendar_uri', Types::STRING, [
				'notnull' => false,
				'length' => 255,
			]);
			$table->addColumn('task_uid', Types::STRING, [
				'notnull' => false,
				'length' => 255,
			]);
			$table->setPrimaryKey(['id']);
			$table->addIndex(['timebox_id'], 'timebox_items_tbx_idx');
		}

		return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $closure
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $closure, array $options): void {
	}
}