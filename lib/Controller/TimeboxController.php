<?php
/**
 * Nextcloud - TimeBox
 */

namespace OCA\TimeBox\Controller;

use OCA\TimeBox\Db\TimeBox;
use OCA\TimeBox\Db\TimeBoxMapper;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class TimeboxController extends Controller {
	protected TimeBoxMapper $mapper;
	protected string $userId;

	public function __construct(string $appName, IRequest $request, TimeBoxMapper $mapper, string $userId) {
		parent::__construct($appName, $request);
		$this->mapper = $mapper;
		$this->userId = $userId;
	}

	/**
	 * @NoAdminRequired
	 */
	public function index(): DataResponse {
		$timeboxes = $this->mapper->findByUserId($this->userId);
		return new DataResponse($timeboxes);
	}

	/**
	 * @NoAdminRequired
	 */
	public function create(string $title, string $color = '#0082c9', ?int $startTime = null, ?int $endTime = null): DataResponse {
		$timebox = new TimeBox();
		$timebox->setUserId($this->userId);
		$timebox->setTitle($title);
		$timebox->setColor($color);
		$timebox->setStartTime($startTime ?? 0);
		$timebox->setEndTime($endTime ?? 0);
		$timebox->setCreatedTime(time());
		$timebox = $this->mapper->insert($timebox);
		return new DataResponse($timebox);
	}

	/**
	 * @NoAdminRequired
	 */
	public function update(int $id, string $title, string $color = '#0082c9', ?int $startTime = null, ?int $endTime = null): DataResponse {
		try {
			$timebox = $this->mapper->findByIdAndUserId($id, $this->userId);
		} catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
			return new DataResponse(['error' => 'TimeBox not found'], Http::STATUS_NOT_FOUND);
		}
		$timebox->setTitle($title);
		$timebox->setColor($color);
		$timebox->setStartTime($startTime ?? 0);
		$timebox->setEndTime($endTime ?? 0);
		$timebox = $this->mapper->update($timebox);
		return new DataResponse($timebox);
	}

	/**
	 * @NoAdminRequired
	 */
	public function destroy(int $id): DataResponse {
		try {
			$timebox = $this->mapper->findByIdAndUserId($id, $this->userId);
		} catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
			return new DataResponse(['error' => 'TimeBox not found'], Http::STATUS_NOT_FOUND);
		}
		$this->mapper->delete($timebox);
		return new DataResponse(['success' => true]);
	}

	/**
	 * @NoAdminRequired
	 */
	public function sort(int $id): DataResponse {
		try {
			$timebox = $this->mapper->findByIdAndUserId($id, $this->userId);
		} catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
			return new DataResponse(['error' => 'TimeBox not found'], Http::STATUS_NOT_FOUND);
		}
		return new DataResponse($timebox);
	}
}