<?php
/**
 * Nextcloud - TimeBox
 *
 * Proxies calendar events from Nextcloud's CalDAV server
 */

namespace OCA\TimeBox\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\Calendar\IManager;
use OCP\IRequest;
use Psr\Log\LoggerInterface;

class CalendarController extends Controller {
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
	public function events(): DataResponse {
		try {
			$calendars = $this->calendarManager->getCalendars($this->userId);

			$events = [];

			foreach ($calendars as $calendar) {
				try {
					// 'types' (not 'component_types') is the option key Nextcloud understands
					$searchResult = $calendar->search('', ['SUMMARY'], ['types' => ['VEVENT']], 100);

					foreach ($searchResult as $result) {
						// search() returns the parsed iCalendar objects under 'objects'
						$objects = $result['objects'] ?? [];
						if (empty($objects)) {
							$objects = [[]];
						}

						foreach ($objects as $object) {
							$events[] = [
								'id' => $result['id'] ?? $result['uri'] ?? '',
								'uri' => $result['uri'] ?? '',
								'calendarUri' => $calendar->getUri(),
								'calendarName' => $calendar->getDisplayName(),
								'summary' => $this->getPropValue($object, 'SUMMARY'),
								'description' => $this->getPropValue($object, 'DESCRIPTION'),
								'start' => $this->getDateTime($object, 'DTSTART'),
								'end' => $this->getEnd($object),
								'type' => 'event',
							];
						}
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

	/**
	 * Compute the event end: DTEND if present, otherwise start + DURATION.
	 */
	private function getEnd($object): ?string {
		$end = $this->getDateTime($object, 'DTEND');
		if ($end !== null) {
			return $end;
		}
		$start = $this->getRawProp($object, 'DTSTART');
		$duration = $this->getRawProp($object, 'DURATION');
		if ($start instanceof \DateTimeInterface && $duration instanceof \DateInterval) {
			try {
				return $start->add($duration)->format('c');
			} catch (\Exception $e) {
				return null;
			}
		}
		return null;
	}
}
