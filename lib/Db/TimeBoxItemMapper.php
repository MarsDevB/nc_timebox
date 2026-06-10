<?php
/**
 * Nextcloud - TimeBox
 */

namespace OCA\TimeBox\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class TimeBoxItemMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'timebox_items', TimeBoxItem::class);
	}

	/**
	 * @param int $timeboxId
	 * @return TimeBoxItem[]
	 */
	public function findByTimeboxId(int $timeboxId): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('timebox_id', $qb->createNamedParameter($timeboxId)))
			->orderBy('sort_order', 'ASC');
		return $this->findEntities($qb);
	}

	/**
	 * @param int $id
	 * @return TimeBoxItem
	 */
	public function findById(int $id): TimeBoxItem {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id)));
		return $this->findEntity($qb);
	}
}