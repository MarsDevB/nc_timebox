<?php
/**
 * Nextcloud - TimeBox
 *
 * This file is licensed under the GNU Affero General Public License version 3 or
 * later. See the LICENSE file.
 */

namespace OCA\TimeBox\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;

class Application extends App implements IBootstrap {
	public const APP_ID = 'timebox';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
		// Register services, listeners, etc.
	}

	public function boot(IBootContext $context): void {
		// Boot logic
	}
}