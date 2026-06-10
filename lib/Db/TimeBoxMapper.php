<?php
/**
 * Nextcloud - TimeBox
 */

namespace OCA\TimeBox\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\IDBConnection;

class TimeBoxMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'timebox_timeboxes', TimeBox::class);
	}

	/**
	 * @param string $userId
	 * @return TimeBox[]
	 */
	public function findByUserId(string $userId): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)))
			->orderBy('created_time', 'ASC');
		return $this->findEntities($qb);
	}

	/**
	 * @param int $id
	 * @param string $userId
	 * @return TimeBox
	 * @throws \OCP\AppFramework\Db\DoesNotExistException
	 */
	public function findByIdAndUserId(int $id, string $userId): TimeBox {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id)))
			->andWhere($qb->expr()->eq('user_id', $qb->createNamedParameter($userId)));
		return $this->findEntity($qb);
	}
}