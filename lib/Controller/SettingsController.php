<?php
/**
 * Nextcloud - TimeBox
 */

namespace OCA\TimeBox\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IConfig;
use OCP\IRequest;

class SettingsController extends Controller {
protected IConfig $config;
protected string $userId;

public function __construct(string $appName, IRequest $request, IConfig $config, string $userId) {
parent::__construct($appName, $request);
$this->config = $config;
$this->userId = $userId;
}

/**
 * @NoAdminRequired
 */
public function get(): DataResponse {
return new DataResponse([
'maxItems' => (int)$this->config->getUserValue($this->userId, 'timebox', 'maxItems', '50'),
'maxEvents' => (int)$this->config->getUserValue($this->userId, 'timebox', 'maxEvents', '50'),
]);
}

/**
 * @NoAdminRequired
 */
public function save(int $maxItems = 50, int $maxEvents = 50): DataResponse {
$maxItems = max(1, min(500, $maxItems));
$maxEvents = max(1, min(500, $maxEvents));
$this->config->setUserValue($this->userId, 'timebox', 'maxItems', (string)$maxItems);
$this->config->setUserValue($this->userId, 'timebox', 'maxEvents', (string)$maxEvents);
return new DataResponse(['success' => true, 'maxItems' => $maxItems, 'maxEvents' => $maxEvents]);
}
}
