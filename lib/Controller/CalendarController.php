<?php
/**
 * Nextcloud - TimeBox
 * 
 * Proxies calendar events from Nextcloud's CalDAV server
 */

namespace OCA\TimeBox\Controller;

use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\DataResponse;
use OCP\Calendar\IManager;
use OCP\IRequest;
use Psr\Log\LoggerInterface;

class CalendarController extends ApiController {
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
	public function events(): DataResponse {
		try {
			$calendars = $this->calendarManager->getCalendars($this->userId);
			
			$events = [];
			$now = new \DateTime();
			$startRange = clone $now;
			$startRange->modify('-7 days');
			$endRange = clone $now;
			$endRange->modify('+30 days');
			
			foreach ($calendars as $calendar) {
				try {
					// Use search to find events in the date range
					$searchResult = $calendar->search('', ['SUMMARY'], 50);
					
					foreach ($searchResult as $event) {
						$events[] = [
							'id' => $event['id'] ?? $event['uri'] ?? '',
							'uri' => $event['uri'] ?? '',
							'calendarUri' => $calendar->getUri(),
							'calendarName' => $calendar->getDisplayName(),
							'summary' => $event['summary'] ?? $event['SUMMARY'] ?? '',
							'description' => $event['description'] ?? $event['DESCRIPTION'] ?? '',
							'start' => isset($event['start']) ? $event['start']->format('c') : null,
							'end' => isset($event['end']) ? $event['end']->format('c') : null,
							'type' => 'event',
						];
					}
				} catch (\Exception $e) {
					$this->logger->debug('TimeBox: Error reading calendar ' . $calendar->getUri() . ': ' . $e->getMessage(), ['app' => 'timebox']);
				}
			}
			
			return new DataResponse($events);
		} catch (\Exception $e) {
			$this->logger->warning('TimeBox: Could not fetch calendar events: ' . $e->getMessage(), ['app' => 'timebox']);
			return new DataResponse([]);
		}
	}
}