<?php
/**
 * Nextcloud - TimeBox
 */

namespace OCA\TimeBox\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method int getTimeboxId()
 * @method void setTimeboxId(int $timeboxId)
 * @method string getItemType()
 * @method void setItemType(string $itemType)
 * @method string getItemSourceId()
 * @method void setItemSourceId(string $itemSourceId)
 * @method string getTitle()
 * @method void setTitle(string $title)
 * @method string getDescription()
 * @method void setDescription(string $description)
 * @method int getSortOrder()
 * @method void setSortOrder(int $sortOrder)
 * @method string getCalendarUri()
 * @method void setCalendarUri(string $calendarUri)
 * @method string getTaskUid()
 * @method void setTaskUid(string $taskUid)
 */
class TimeBoxItem extends Entity implements \JsonSerializable {
	protected $timeboxId;
	protected $itemType; // 'task' or 'event'
	protected $itemSourceId;
	protected $title;
	protected $description = '';
	protected $sortOrder = 0;
	protected $calendarUri = '';
	protected $taskUid = '';

	public function __construct() {
		$this->addType('timeboxId', 'integer');
		$this->addType('sortOrder', 'integer');
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'timeboxId' => $this->timeboxId,
			'itemType' => $this->itemType,
			'itemSourceId' => $this->itemSourceId,
			'title' => $this->title,
			'description' => $this->description,
			'sortOrder' => $this->sortOrder,
			'calendarUri' => $this->calendarUri,
			'taskUid' => $this->taskUid,
		];
	}
}