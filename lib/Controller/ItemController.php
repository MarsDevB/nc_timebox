<?php
/**
 * Nextcloud - TimeBox
 */

namespace OCA\TimeBox\Controller;

use OCA\TimeBox\Db\TimeBoxItem;
use OCA\TimeBox\Db\TimeBoxItemMapper;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class ItemController extends Controller {
	protected TimeBoxItemMapper $mapper;
	protected string $userId;

	public function __construct(string $appName, IRequest $request, TimeBoxItemMapper $mapper, string $userId) {
		parent::__construct($appName, $request);
		$this->mapper = $mapper;
		$this->userId = $userId;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(int $timeboxId): DataResponse {
		$items = $this->mapper->findByTimeboxId($timeboxId);
		return new DataResponse($items);
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(int $timeboxId, string $itemType, string $title, string $description = '', string $itemSourceId = '', string $calendarUri = '', string $taskUid = '', int $sortOrder = 0, bool $completed = false): DataResponse {
		$item = new TimeBoxItem();
		$item->setTimeboxId($timeboxId);
		$item->setItemType($itemType);
		$item->setTitle($title);
		$item->setDescription($description);
		$item->setItemSourceId($itemSourceId);
		$item->setCalendarUri($calendarUri);
		$item->setTaskUid($taskUid);
		$item->setSortOrder($sortOrder);
		$item->setCompleted($completed);
		$item = $this->mapper->insert($item);
		return new DataResponse($item);
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $timeboxId, int $id, string $itemType, string $title, string $description = '', string $itemSourceId = '', string $calendarUri = '', string $taskUid = '', int $sortOrder = 0, bool $completed = false): DataResponse {
		try {
			$item = $this->mapper->findById($id);
		} catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
			return new DataResponse(['error' => 'Item not found'], Http::STATUS_NOT_FOUND);
		}
		$item->setTimeboxId($timeboxId);
		$item->setItemType($itemType);
		$item->setTitle($title);
		$item->setDescription($description);
		$item->setItemSourceId($itemSourceId);
		$item->setCalendarUri($calendarUri);
		$item->setTaskUid($taskUid);
		$item->setSortOrder($sortOrder);
		$item->setCompleted($completed);
		$item = $this->mapper->update($item);
		return new DataResponse($item);
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $timeboxId, int $id): DataResponse {
		try {
			$item = $this->mapper->findById($id);
		} catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
			return new DataResponse(['error' => 'Item not found'], Http::STATUS_NOT_FOUND);
		}
		$this->mapper->delete($item);
		return new DataResponse(['success' => true]);
	}

	/**
	 * @NoAdminRequired
	 */
	public function reorder(int $timeboxId, array $itemIds): DataResponse {
		$sortOrder = 0;
		foreach ($itemIds as $id) {
			try {
				$item = $this->mapper->findById((int)$id);
				$item->setSortOrder($sortOrder);
				$this->mapper->update($item);
				$sortOrder++;
			} catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
				// Skip missing items
			}
		}
		return new DataResponse(['success' => true]);
	}
}