<?php
/**
 * Nextcloud - TimeBox
 *
 * This file is licensed under the GNU Affero General Public License version 3 or
 * later. See the LICENSE file.
 */

return [
	'routes' => [
		// Main page
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],

		// TimeBox CRUD
		['name' => 'timebox#index', 'url' => '/timeboxes', 'verb' => 'GET'],
		['name' => 'timebox#create', 'url' => '/timeboxes', 'verb' => 'POST'],
		['name' => 'timebox#update', 'url' => '/timeboxes/{id}', 'verb' => 'PUT'],
		['name' => 'timebox#destroy', 'url' => '/timeboxes/{id}', 'verb' => 'DELETE'],
		['name' => 'timebox#sort', 'url' => '/timeboxes/{id}/sort', 'verb' => 'PUT'],

		// TimeBox Items - reorder MUST come before {id} routes to avoid greedy matching
		['name' => 'item#index', 'url' => '/timeboxes/{timeboxId}/items', 'verb' => 'GET'],
		['name' => 'item#create', 'url' => '/timeboxes/{timeboxId}/items', 'verb' => 'POST'],
		['name' => 'item#reorder', 'url' => '/timeboxes/{timeboxId}/items/reorder', 'verb' => 'PUT'],
		['name' => 'item#update', 'url' => '/timeboxes/{timeboxId}/items/{id}', 'verb' => 'PUT', 'requirements' => ['id' => '\d+']],
		['name' => 'item#destroy', 'url' => '/timeboxes/{timeboxId}/items/{id}', 'verb' => 'DELETE', 'requirements' => ['id' => '\d+']],

		// Calendar events proxy (fetch from CalDAV)
		['name' => 'calendar#events', 'url' => '/calendar/events', 'verb' => 'GET'],

		// Tasks proxy (fetch from Tasks API)
		['name' => 'task#tasks', 'url' => '/tasks', 'verb' => 'GET'],
	]
];