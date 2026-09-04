<?php
/**
 * Nextcloud - TimeBox
 */

namespace OCA\TimeBox\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\Settings\ISettings;

class Personal implements ISettings {
protected IConfig $config;
protected string $userId;

public function __construct(IConfig $config, string $userId) {
$this->config = $config;
$this->userId = $userId;
}

public function getForm(): TemplateResponse {
$params = [
'maxItems' => (int)$this->config->getUserValue($this->userId, 'timebox', 'maxItems', '50'),
'maxEvents' => (int)$this->config->getUserValue($this->userId, 'timebox', 'maxEvents', '50'),
];
return new TemplateResponse('timebox', 'settings/personal', $params);
}

public function getSection(): string {
return 'additional';
}

public function getPriority(): int {
return 50;
}
}
