<?php
/**
 * Nextcloud - TimeBox
 */

namespace OCA\TimeBox\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method string getTitle()
 * @method void setTitle(string $title)
 * @method string getColor()
 * @method void setColor(string $color)
 * @method int getStartTime()
 * @method void setStartTime(int $startTime)
 * @method int getEndTime()
 * @method void setEndTime(int $endTime)
 * @method int getCreatedTime()
 * @method void setCreatedTime(int $createdTime)
 */
class TimeBox extends Entity implements \JsonSerializable {
	protected $userId;
	protected $title;
	protected $color = '#0082c9';
	protected $startTime;
	protected $endTime;
	protected $createdTime;

	public function __construct() {
		$this->addType('startTime', 'integer');
		$this->addType('endTime', 'integer');
		$this->addType('createdTime', 'integer');
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'userId' => $this->userId,
			'title' => $this->title,
			'color' => $this->color,
			'startTime' => $this->startTime,
			'endTime' => $this->endTime,
			'createdTime' => $this->createdTime,
		];
	}
}