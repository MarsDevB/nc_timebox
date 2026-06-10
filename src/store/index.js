/**
 * Nextcloud - TimeBox
 *
 * Reactive store for TimeBox app state (Vue 3 reactive).
 */

import { reactive, readonly } from 'vue'
import api from '../services/api.js'

const state = reactive({
	timeboxes: [],
	activeTimeboxId: null,
	items: {},        // { timeboxId: [items] }
	calendarEvents: [],
	tasks: [],
	loading: false,
	error: null,
})

const actions = {
	async fetchTimeboxes() {
		state.loading = true
		state.error = null
		try {
			state.timeboxes = await api.getTimeboxes()
			// Also fetch items for each timebox
			for (const tb of state.timeboxes) {
				state.items[tb.id] = await api.getItems(tb.id)
			}
		} catch (e) {
			state.error = e.message || 'Failed to load timeboxes'
			console.error('TimeBox: fetchTimeboxes error:', e)
		} finally {
			state.loading = false
		}
	},

	async createTimebox(data) {
		try {
			const timebox = await api.createTimebox(data)
			state.timeboxes.push(timebox)
			state.items[timebox.id] = []
			return timebox
		} catch (e) {
			state.error = e.message || 'Failed to create timebox'
			console.error('TimeBox: createTimebox error:', e)
		}
	},

	async updateTimebox(id, data) {
		try {
			const updated = await api.updateTimebox(id, data)
			const idx = state.timeboxes.findIndex(tb => tb.id === id)
			if (idx !== -1) {
				state.timeboxes[idx] = updated
			}
			return updated
		} catch (e) {
			state.error = e.message || 'Failed to update timebox'
			console.error('TimeBox: updateTimebox error:', e)
		}
	},

	async deleteTimebox(id) {
		try {
			await api.deleteTimebox(id)
			state.timeboxes = state.timeboxes.filter(tb => tb.id !== id)
			delete state.items[id]
			if (state.activeTimeboxId === id) {
				state.activeTimeboxId = null
			}
		} catch (e) {
			state.error = e.message || 'Failed to delete timebox'
			console.error('TimeBox: deleteTimebox error:', e)
		}
	},

	setActiveTimebox(id) {
		state.activeTimeboxId = id
	},

	async addItem(timeboxId, data) {
		try {
			const item = await api.createItem(timeboxId, data)
			if (!state.items[timeboxId]) {
				state.items[timeboxId] = []
			}
			state.items[timeboxId].push(item)
			return item
		} catch (e) {
			state.error = e.message || 'Failed to add item'
			console.error('TimeBox: addItem error:', e)
		}
	},

	async updateItem(timeboxId, id, data) {
		try {
			const updated = await api.updateItem(timeboxId, id, data)
			if (state.items[timeboxId]) {
				const idx = state.items[timeboxId].findIndex(i => i.id === id)
				if (idx !== -1) {
					state.items[timeboxId][idx] = updated
				}
			}
			return updated
		} catch (e) {
			state.error = e.message || 'Failed to update item'
			console.error('TimeBox: updateItem error:', e)
		}
	},

	async deleteItem(timeboxId, id) {
		try {
			await api.deleteItem(timeboxId, id)
			if (state.items[timeboxId]) {
				state.items[timeboxId] = state.items[timeboxId].filter(i => i.id !== id)
			}
		} catch (e) {
			state.error = e.message || 'Failed to delete item'
			console.error('TimeBox: deleteItem error:', e)
		}
	},

	async reorderItems(timeboxId, itemIds) {
		try {
			await api.reorderItems(timeboxId, itemIds)
			// Reorder local state
			if (state.items[timeboxId]) {
				const reordered = []
				itemIds.forEach(id => {
					const item = state.items[timeboxId].find(i => i.id === id)
					if (item) {
						reordered.push({ ...item, sortOrder: reordered.length })
					}
				})
				state.items[timeboxId] = reordered
			}
		} catch (e) {
			state.error = e.message || 'Failed to reorder items'
			console.error('TimeBox: reorderItems error:', e)
		}
	},

	async fetchCalendarEvents() {
		try {
			state.calendarEvents = await api.getCalendarEvents()
		} catch (e) {
			console.error('TimeBox: fetchCalendarEvents error:', e)
			state.calendarEvents = []
		}
	},

	async fetchTasks() {
		try {
			state.tasks = await api.getTasks()
		} catch (e) {
			console.error('TimeBox: fetchTasks error:', e)
			state.tasks = []
		}
	},
}

export const store = {
	state: readonly(state),
	...actions,
}

export default store