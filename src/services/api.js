/**
 * Nextcloud - TimeBox
 *
 * API service for communicating with the TimeBox backend.
 */

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

const BASE_URL = generateUrl('/apps/timebox')

export default {
	// TimeBox CRUD
	getTimeboxes() {
		return axios.get(`${BASE_URL}/timeboxes`).then(res => res.data)
	},

	createTimebox(data) {
		return axios.post(`${BASE_URL}/timeboxes`, data).then(res => res.data)
	},

	updateTimebox(id, data) {
		return axios.put(`${BASE_URL}/timeboxes/${id}`, data).then(res => res.data)
	},

	deleteTimebox(id) {
		return axios.delete(`${BASE_URL}/timeboxes/${id}`).then(res => res.data)
	},

	// TimeBox Items
	getItems(timeboxId) {
		return axios.get(`${BASE_URL}/timeboxes/${timeboxId}/items`).then(res => res.data)
	},

	createItem(timeboxId, data) {
		return axios.post(`${BASE_URL}/timeboxes/${timeboxId}/items`, data).then(res => res.data)
	},

	updateItem(timeboxId, id, data) {
		return axios.put(`${BASE_URL}/timeboxes/${timeboxId}/items/${id}`, data).then(res => res.data)
	},

	deleteItem(timeboxId, id) {
		return axios.delete(`${BASE_URL}/timeboxes/${timeboxId}/items/${id}`).then(res => res.data)
	},

	reorderItems(timeboxId, itemIds) {
		return axios.put(`${BASE_URL}/timeboxes/${timeboxId}/items/reorder`, { itemIds }).then(res => res.data)
	},

	// Calendar events
	getCalendarEvents() {
		return axios.get(`${BASE_URL}/calendar/events`).then(res => res.data)
	},

	// Tasks
	getTasks() {
		return axios.get(`${BASE_URL}/tasks`).then(res => res.data)
	},

	// User settings
	getSettings() {
		return axios.get(`${BASE_URL}/settings`).then(res => res.data)
	},

	saveSettings(data) {
		return axios.post(`${BASE_URL}/settings`, data).then(res => res.data)
	},
}