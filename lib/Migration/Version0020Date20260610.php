<?php
/**
 * Nextcloud - TimeBox
 *
 * Migration to add completed column to timebox_items table.
 */

namespace OCA\TimeBox\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version0020Date20260610 extends SimpleMigrationStep {

/**
 * @param IOutput $output
 * @param Closure $closure
 * @param array $options
 * @return ISchemaWrapper
 */
public function changeSchema(IOutput $output, Closure $closure, array $options): ISchemaWrapper {
/** @var ISchemaWrapper $schema */
$schema = $closure();

if ($schema->hasTable('timebox_items')) {
$table = $schema->getTable('timebox_items');
if (!$table->hasColumn('completed')) {
$table->addColumn('completed', Types::BOOLEAN, [
'notnull' => true,
'default' => false,
]);
}
}

return $schema;
}
}
