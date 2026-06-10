<?php
/**
 * Nextcloud - TimeBox
 * 
 * Proxies tasks from Nextcloud's built-in task system (CalDAV VTODO)
 */

namespace OCA\TimeBox\Controller;

use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\DataResponse;
use OCP\Calendar\IManager;
use OCP\IRequest;
use Psr\Log\LoggerInterface;

class TaskController extends ApiController {
	protected string $userId;
	protected IManager $calendarManager;
	protected LoggerInterface $logger;

	public function __construct(string $appName, IRequest $request, string $userId, IManager $calendarManager, LoggerInterface $logger) {
		parent::__construct($appName, $request);
		$this->userId = $userId;
		$this->calendarManager = $calendarManager;
		$this->logger = $logger;
	}

	/**
	 * @NoAdminRequired
	 */
	public function tasks(): DataResponse {
		try {
			$calendars = $this->calendarManager->getCalendars($this->userId);
			
			$tasks = [];
			
			foreach ($calendars as $calendar) {
				try {
					// Search for VTODO items in this calendar
					$searchResult = $calendar->search('', ['SUMMARY'], 100);
					
					foreach ($searchResult as $task) {
						// Check if this is a VTODO (task) type
						$uri = $task['uri'] ?? '';
						if (strpos($uri, '.ics') === false) {
							continue;
						}
						
						$tasks[] = [
							'id' => $task['id'] ?? $uri,
							'uri' => $uri,
							'calendarUri' => $calendar->getUri(),
							'calendarName' => $calendar->getDisplayName(),
							'summary' => $task['summary'] ?? $task['SUMMARY'] ?? '',
							'description' => $task['description'] ?? $task['DESCRIPTION'] ?? '',
							'status' => $task['status'] ?? $task['STATUS'] ?? '',
							'priority' => $task['priority'] ?? $task['PRIORITY'] ?? 0,
							'due' => isset($task['due']) ? (is_object($task['due']) ? $task['due']->format('c') : $task['due']) : null,
							'start' => isset($task['start']) ? (is_object($task['start']) ? $task['start']->format('c') : $task['start']) : null,
							'completed' => $task['completed'] ?? false,
							'type' => 'task',
						];
					}
				} catch (\Exception $e) {
					$this->logger->debug('TimeBox: Error reading tasks from calendar ' . $calendar->getUri() . ': ' . $e->getMessage(), ['app' => 'timebox']);
				}
			}
			
			return new DataResponse($tasks);
		} catch (\Exception $e) {
			$this->logger->warning('TimeBox: Could not fetch tasks: ' . $e->getMessage(), ['app' => 'timebox']);
			return new DataResponse([]);
		}
	}
}