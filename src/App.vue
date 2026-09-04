<template>
	<div id="timebox-app-content" class="timebox-app-wrapper">
		<AppNavigation>
			<template #list>
				<TimeBoxList
					:timeboxes="state.timeboxes"
					:active-id="state.activeTimeboxId"
					@select="setActiveTimebox"
					@create="handleCreateTimebox"
					@delete="handleDeleteTimebox"
				/>
			</template>
		</AppNavigation>
		<AppContent>
			<div class="timebox-main">
				<div v-if="state.loading" class="loading-indicator">
					<div class="timebox-loading"></div>
					<p>Loading timeboxes...</p>
				</div>
				<div v-else-if="state.error" class="error-message">
					<div class="error-card">
						<p>{{ state.error }}</p>
					</div>
				</div>
				<template v-else-if="activeTimebox">
					<TimeBoxDetail
						:timebox="activeTimebox"
						:items="activeItems"
						:calendar-events="state.calendarEvents"
						:tasks="state.tasks"
						:max-items="maxItems"
						:max-events="maxEvents"
						@update="handleUpdateTimebox"
						@delete-item="handleDeleteItem"
						@add-item="handleAddItem"
						@reorder-items="handleReorderItems"
						@toggle-item="handleToggleItem"
					/>
				</template>
				<div v-else class="empty-state">
					<div class="empty-state__icon">⏱</div>
					<h2>TimeBox</h2>
					<p>Select or create a timebox to start organizing your tasks and calendar events.</p>
				</div>
			</div>
		</AppContent>
	</div>
</template>

<script>
import { onMounted, ref, computed } from 'vue'
import store from './store/index.js'
import api from './services/api.js'
import AppNavigation from './components/AppNavigation.vue'
import AppContent from './components/AppContent.vue'
import TimeBoxList from './components/TimeBoxList.vue'
import TimeBoxDetail from './components/TimeBoxDetail.vue'

export default {
	name: 'App',
	components: {
		AppNavigation,
		AppContent,
		TimeBoxList,
		TimeBoxDetail,
	},
	setup() {
		const { state, ...actions } = store

		function loadInitialInt(key, fallback) {
			try {
				const value = window.OCP?.InitialState?.loadValue?.('timebox', key)
				const parsed = parseInt(value, 10)
				return Number.isFinite(parsed) && parsed > 0 ? parsed : fallback
			} catch (e) {
				return fallback
			}
		}
		const maxItems = ref(loadInitialInt('maxItems', 50))
		const maxEvents = ref(loadInitialInt('maxEvents', 50))

		onMounted(async () => {
			// Load limits from the API (source of truth) – falls back to initial state values
			try {
				const settings = await api.getSettings()
				if (Number.isFinite(parseInt(settings?.maxItems, 10)) && parseInt(settings.maxItems, 10) > 0) {
					maxItems.value = parseInt(settings.maxItems, 10)
				}
				if (Number.isFinite(parseInt(settings?.maxEvents, 10)) && parseInt(settings.maxEvents, 10) > 0) {
					maxEvents.value = parseInt(settings.maxEvents, 10)
				}
			} catch (e) {
				// Keep initial state / default values
			}
			await actions.fetchTimeboxes()
			await actions.fetchCalendarEvents()
			await actions.fetchTasks()
		})

		const activeTimebox = computed(() => {
			if (!state.activeTimeboxId) return null
			return state.timeboxes.find(tb => tb.id === state.activeTimeboxId) || null
		})

		const activeItems = computed(() => {
			if (!state.activeTimeboxId || !state.items[state.activeTimeboxId]) return []
			return state.items[state.activeTimeboxId]
		})

		async function handleCreateTimebox(data) {
			const timebox = await actions.createTimebox(data)
			if (timebox) {
				actions.setActiveTimebox(timebox.id)
			}
		}

		async function handleUpdateTimebox(id, data) {
			await actions.updateTimebox(id, data)
		}

		async function handleDeleteTimebox(id) {
			await actions.deleteTimebox(id)
		}

		async function handleAddItem(timeboxId, data) {
			await actions.addItem(timeboxId, data)
		}

		async function handleDeleteItem(timeboxId, itemId) {
			await actions.deleteItem(timeboxId, itemId)
		}

		async function handleReorderItems(timeboxId, itemIds) {
			await actions.reorderItems(timeboxId, itemIds)
		}

		async function handleToggleItem(timeboxId, itemId, data) {
			await actions.updateItem(timeboxId, itemId, data)
		}

		function setActiveTimebox(id) {
			actions.setActiveTimebox(id)
		}

		return {
			state,
			activeTimebox,
			activeItems,
			maxItems,
			maxEvents,
			setActiveTimebox,
			handleCreateTimebox,
			handleUpdateTimebox,
			handleDeleteTimebox,
			handleAddItem,
			handleDeleteItem,
			handleReorderItems,
			handleToggleItem,
		}
	},
}
</script>

<style scoped>
.timebox-main {
	padding: 0;
	height: 100%;
	display: flex;
	flex-direction: column;
}

.loading-indicator {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	height: 200px;
	color: var(--color-text-maxcontrast);
}

.error-message {
	padding: 20px;
}

.error-card {
	background-color: var(--color-error);
	color: white;
	padding: 16px 20px;
	border-radius: var(--border-radius);
	margin: 10px 0;
}

.empty-state {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	height: 60vh;
	color: var(--color-text-maxcontrast);
}

.empty-state__icon {
	font-size: 64px;
	margin-bottom: 20px;
}

.empty-state h2 {
	font-size: 28px;
	font-weight: 300;
	margin-bottom: 10px;
	color: var(--color-text-light);
}

.empty-state p {
	font-size: 16px;
	max-width: 400px;
	text-align: center;
}
</style>