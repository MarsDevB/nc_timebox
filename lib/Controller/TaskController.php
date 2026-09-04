<?php
/**
 * Nextcloud - TimeBox
 *
 * Proxies tasks from Nextcloud's task system (CalDAV VTODO)
 */

namespace OCA\TimeBox\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\Calendar\IManager;
use OCP\IRequest;
use Psr\Log\LoggerInterface;

class TaskController extends Controller {
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
	 * @NoCSRFRequired
	 */
	public function tasks(): DataResponse {
		try {
			$calendars = $this->calendarManager->getCalendars($this->userId);

			$tasks = [];

			foreach ($calendars as $calendar) {
				try {
					// 'types' (not 'component_types') is the option key Nextcloud understands
					$searchResult = $calendar->search('', ['SUMMARY'], ['types' => ['VTODO']], 100);

					foreach ($searchResult as $result) {
						// search() returns the parsed iCalendar objects under 'objects'
						$objects = $result['objects'] ?? [];
						if (empty($objects)) {
							$objects = [null];
						}

						foreach ($objects as $object) {
							$percentComplete = $this->getIntProp($object, 'PERCENT-COMPLETE');
							$status = $this->getPropValue($object, 'STATUS');

							// Only show open tasks - skip completed ones
							if ($status === 'COMPLETED' || $percentComplete >= 100) {
								continue;
							}

							$tasks[] = [
								'id' => $result['id'] ?? $result['uri'] ?? '',
								'uri' => $result['uri'] ?? '',
								'calendarUri' => $calendar->getUri(),
								'calendarName' => $calendar->getDisplayName(),
								'summary' => $this->getPropValue($object, 'SUMMARY'),
								'description' => $this->getPropValue($object, 'DESCRIPTION'),
								'due' => $this->getDateTime($object, 'DUE'),
								'priority' => $this->getIntProp($object, 'PRIORITY'),
								'percentComplete' => $percentComplete,
								'status' => $status ?: null,
								'type' => 'task',
							];
						}
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

	/**
	 * Read a property from a parsed calendar object.
	 *
	 * Nextcloud's IManager::search() may return the objects either as Sabre
	 * VObject components or as plain arrays keyed by property name where each
	 * value is a [value, parameters] tuple. This helper supports both shapes.
	 *
	 * @param mixed $object
	 * @return mixed|null The raw property value or null if not present
	 */
	private function getRawProp($object, string $prop) {
		if (is_object($object)) {
			if (!isset($object->{$prop})) {
				return null;
			}
			try {
				return $object->{$prop}->getValue();
			} catch (\Exception $e) {
				return null;
			}
		}
		if (is_array($object) && isset($object[$prop]) && is_array($object[$prop])) {
			return $object[$prop][0] ?? null;
		}
		return null;
	}

	/**
	 * Read a string property.
	 */
	private function getPropValue($object, string $prop): string {
		$value = $this->getRawProp($object, $prop);
		return is_string($value) ? $value : (is_scalar($value) ? (string)$value : '');
	}

	/**
	 * Read an integer property.
	 */
	private function getIntProp($object, string $prop): ?int {
		$value = $this->getRawProp($object, $prop);
		return is_numeric($value) ? (int)$value : null;
	}

	/**
	 * Read a date/time property and return it as ISO 8601 string.
	 */
	private function getDateTime($object, string $prop): ?string {
		$value = $this->getRawProp($object, $prop);
		if ($value instanceof \DateTimeInterface) {
			return $value->format('c');
		}
		if (is_string($value) && $value !== '') {
			try {
				return (new \DateTimeImmutable($value))->format('c');
			} catch (\Exception $e) {
				return null;
			}
		}
		return null;
	}
}
