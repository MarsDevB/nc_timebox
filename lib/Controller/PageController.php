<?php
/**
 * Nextcloud - TimeBox
 */

namespace OCA\TimeBox\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IInitialStateService;
use OCP\IRequest;

class PageController extends Controller {
protected IConfig $config;
protected IInitialStateService $initialState;
protected string $userId;

public function __construct(string $appName, IRequest $request, IConfig $config, IInitialStateService $initialState, string $userId) {
parent::__construct($appName, $request);
$this->config = $config;
$this->initialState = $initialState;
$this->userId = $userId;
}

/**
 * @NoAdminRequired
 * @NoCSRFRequired
 */
public function index(): TemplateResponse {
$this->initialState->provideInitialState(
'timebox',
'maxItems',
(int)$this->config->getUserValue($this->userId, 'timebox', 'maxItems', '50')
);
$this->initialState->provideInitialState(
'timebox',
'maxEvents',
(int)$this->config->getUserValue($this->userId, 'timebox', 'maxEvents', '50')
);
return new TemplateResponse('timebox', 'main', []);
}
}
