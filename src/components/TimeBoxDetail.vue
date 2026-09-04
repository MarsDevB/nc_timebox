<template>
	<div class="timebox-detail">
		<div class="timebox-detail__header">
			<div class="timebox-detail__title-row">
				<span class="timebox-detail__color-badge" :style="{ backgroundColor: timebox.color }"></span>
				<h2 v-if="!editing" class="timebox-detail__title">{{ timebox.title }}</h2>
				<input
					v-else
					v-model="editTitle"
					class="timebox-detail__title-input"
					@keyup.enter="saveTitle"
					@keyup.escape="cancelEdit"
					ref="editInput"
				/>
				<button v-if="!editing" class="timebox-detail__edit-btn" @click="startEdit" title="Edit title">✏️</button>
				<button v-if="editing" class="timebox-detail__edit-btn" @click="saveTitle" title="Save">✓</button>
				<button v-if="editing" class="timebox-detail__edit-btn" @click="cancelEdit" title="Cancel">✗</button>
			</div>
		</div>

		<div class="timebox-detail__content">
			<!-- Left column: Items in this timebox (sortable) -->
			<div class="timebox-detail__column">
				<h3 class="timebox-detail__column-title">
					📋 TimeBox Items
					<span class="timebox-detail__count">{{ items.length }}</span>
				</h3>
				<SortableList
					:items="items"
					@reorder="handleReorder"
					@delete="handleDeleteItem"
					@toggle-completed="handleToggleCompleted"
				/>
				<div v-if="items.length === 0" class="timebox-detail__empty-column">
					<p>Drag tasks or events here, or add from the right panel.</p>
				</div>
			</div>

			<!-- Right column: Available tasks and events -->
			<div class="timebox-detail__column">
				<h3 class="timebox-detail__column-title">
					📅 Available Items
				</h3>

				<div class="timebox-detail__source-section">
					<h4 class="timebox-detail__source-heading">Add Custom Item</h4>
					<div class="timebox-detail__manual-add">
						<input
							v-model="manualTitle"
							type="text"
							placeholder="Item title..."
							class="timebox-detail__manual-input"
							@keyup.enter="addManualItem"
						/>
						<select v-model="manualType" class="timebox-detail__manual-select">
							<option value="task">Task</option>
							<option value="event">Event</option>
							<option value="note">Note</option>
						</select>
						<button class="primary" @click="addManualItem">Add</button>
					</div>
				</div>

				<div class="timebox-detail__source-section">
					<h4 class="timebox-detail__source-heading">Tasks</h4>
					<ul class="timebox-detail__source-list" v-if="sortedTasks.length > 0">
						<li
							v-for="task in sortedTasks"
							:key="'task-' + (task.id || task.uri)"
							class="timebox-detail__source-item timebox-detail__source-item--task"
							@click="addTaskToTimebox(task)"
						>
							<span class="timebox-detail__item-icon">☑</span>
							<span class="timebox-detail__item-name">{{ task.summary }}</span>
							<span v-if="task.due" class="timebox-detail__item-date" :class="{ 'timebox-detail__item-date--overdue': isOverdue(task.due) }">
								{{ formatDate(task.due) }}
							</span>
							<button class="timebox-detail__add-btn" title="Add to timebox">+</button>
						</li>
					</ul>
					<p v-else class="timebox-detail__no-items">No tasks found</p>
				</div>

				<div class="timebox-detail__source-section">
					<h4 class="timebox-detail__source-heading">Calendar Events</h4>
					<ul class="timebox-detail__source-list" v-if="sortedEvents.length > 0">
						<li
							v-for="event in sortedEvents"
							:key="'event-' + (event.id || event.uri)"
							class="timebox-detail__source-item timebox-detail__source-item--event"
							@click="addEventToTimebox(event)"
						>
							<span class="timebox-detail__item-icon">📅</span>
							<span class="timebox-detail__item-name">{{ event.summary }}</span>
							<span v-if="event.start" class="timebox-detail__item-date" :class="{ 'timebox-detail__item-date--past': isPast(event.end || event.start) }">
								{{ formatEventTime(event) }}
							</span>
							<button class="timebox-detail__add-btn" title="Add to timebox">+</button>
						</li>
					</ul>
					<p v-else class="timebox-detail__no-items">No calendar events found</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import { ref, computed } from 'vue'
import SortableList from './SortableList.vue'

export default {
	name: 'TimeBoxDetail',
	components: {
		SortableList,
	},
	props: {
		timebox: {
			type: Object,
			required: true,
		},
		items: {
			type: Array,
			default: () => [],
		},
		calendarEvents: {
			type: Array,
			default: () => [],
		},
		tasks: {
			type: Array,
			default: () => [],
		},
		maxItems: {
			type: Number,
			default: 50,
		},
		maxEvents: {
			type: Number,
			default: 50,
		},
	},
	emits: ['update', 'delete-item', 'add-item', 'reorder-items', 'toggle-item'],
	setup(props, { emit }) {
		const editing = ref(false)
		const editTitle = ref('')
		const editInput = ref(null)
		const manualTitle = ref('')
		const manualType = ref('task')

		// Sort by due/start date ascending; items without a date go last
		const sortedTasks = computed(() =>
			[...props.tasks].sort((a, b) => {
				const da = a.due ? new Date(a.due).getTime() : Infinity
				const db = b.due ? new Date(b.due).getTime() : Infinity
				return da - db
			}).slice(0, props.maxItems)
		)

		const sortedEvents = computed(() =>
			[...props.calendarEvents]
				// Only keep events that end today or in the future
				.filter((event) => {
					try {
						const endOrStart = event.end || event.start
						if (!endOrStart) return true
						const end = new Date(endOrStart)
						const today = new Date()
						today.setHours(0, 0, 0, 0)
						return end.getTime() >= today.getTime()
					} catch (e) {
						return true
					}
				})
				.sort((a, b) => {
					const da = a.start ? new Date(a.start).getTime() : Infinity
					const db = b.start ? new Date(b.start).getTime() : Infinity
					return da - db
				})
				.slice(0, props.maxEvents)
		)

		function formatDate(iso) {
			try {
				const d = new Date(iso)
				return d.toLocaleDateString(undefined, { day: '2-digit', month: 'short', year: 'numeric' }) +
					', ' + d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' })
			} catch (e) {
				return ''
			}
		}

		function formatEventTime(event) {
			try {
				const start = new Date(event.start)
				const parts = [start.toLocaleDateString(undefined, { day: '2-digit', month: 'short' })]
				if (event.end) {
					const end = new Date(event.end)
					const sameDay = start.toDateString() === end.toDateString()
					if (sameDay) {
						// Same day: "04. Sep, 10:00 – 11:00"
						return parts[0] + ', ' + start.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' }) +
							' – ' + end.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' })
					}
					// Multi-day: "04. Sep – 05. Sep"
					return parts[0] + ' – ' + end.toLocaleDateString(undefined, { day: '2-digit', month: 'short' })
				}
				return parts[0]
			} catch (e) {
				return ''
			}
		}

		function isOverdue(iso) {
			try {
				return new Date(iso).getTime() < Date.now()
			} catch (e) {
				return false
			}
		}

		function isPast(iso) {
			return isOverdue(iso)
		}

		function startEdit() {
			editing.value = true
			editTitle.value = props.timebox.title
		}

		function cancelEdit() {
			editing.value = false
			editTitle.value = ''
		}

		function saveTitle() {
			if (editTitle.value.trim()) {
				emit('update', props.timebox.id, {
					title: editTitle.value.trim(),
					color: props.timebox.color,
				})
			}
			editing.value = false
		}

		function addTaskToTimebox(task) {
			emit('add-item', props.timebox.id, {
				itemType: 'task',
				title: task.summary || 'Untitled Task',
				description: task.description || '',
				itemSourceId: String(task.id ?? task.uri ?? ''),
				calendarUri: task.calendarUri || '',
				taskUid: task.uri || '',
			})
		}

		function addEventToTimebox(event) {
			emit('add-item', props.timebox.id, {
				itemType: 'event',
				title: event.summary || 'Untitled Event',
				description: event.description || '',
				itemSourceId: String(event.id ?? event.uri ?? ''),
				calendarUri: event.calendarUri || '',
				taskUid: '',
			})
		}

		function addManualItem() {
			if (!manualTitle.value.trim()) return
			emit('add-item', props.timebox.id, {
				itemType: manualType.value,
				title: manualTitle.value.trim(),
				description: '',
				itemSourceId: '',
				calendarUri: '',
				taskUid: '',
			})
			manualTitle.value = ''
		}

		function handleReorder(itemIds) {
			emit('reorder-items', props.timebox.id, itemIds)
		}

		function handleDeleteItem(itemId) {
			emit('delete-item', props.timebox.id, itemId)
		}

		function handleToggleCompleted(item) {
			emit('toggle-item', props.timebox.id, item.id, {
				...item,
				completed: !item.completed,
			})
		}

		return {
			editing,
			editTitle,
			editInput,
			manualTitle,
			manualType,
			startEdit,
			cancelEdit,
			saveTitle,
			addTaskToTimebox,
			addEventToTimebox,
			addManualItem,
			handleReorder,
			handleDeleteItem,
			handleToggleCompleted,
			sortedTasks,
			sortedEvents,
			formatDate,
			formatEventTime,
			isOverdue,
			isPast,
		}
	},
}
</script>

<style scoped>
.timebox-detail {
	height: 100%;
	display: flex;
	flex-direction: column;
}

.timebox-detail__header {
	padding: 20px 20px 10px;
	border-bottom: 1px solid var(--color-border);
	flex-shrink: 0;
}

.timebox-detail__title-row {
	display: flex;
	align-items: center;
	gap: 10px;
}

.timebox-detail__color-badge {
	width: 20px;
	height: 20px;
	border-radius: 50%;
	flex-shrink: 0;
}

.timebox-detail__title {
	font-size: 24px;
	font-weight: 300;
	margin: 0;
	flex: 1;
}

.timebox-detail__title-input {
	font-size: 24px;
	font-weight: 300;
	flex: 1;
	border: none;
	border-bottom: 2px solid var(--color-primary);
	outline: none;
	background: transparent;
}

.timebox-detail__edit-btn {
	background: none;
	border: none;
	font-size: 18px;
	cursor: pointer;
	padding: 4px 8px;
	border-radius: var(--border-radius);
}

.timebox-detail__edit-btn:hover {
	background-color: var(--color-background-hover);
}

.timebox-detail__content {
	display: flex;
	gap: 20px;
	padding: 20px;
	flex: 1;
	min-height: 0;
}

.timebox-detail__column {
	flex: 1;
	min-width: 0;
	overflow-y: auto;
}

.timebox-detail__column-title {
	font-size: 16px;
	font-weight: bold;
	color: var(--color-text-light);
	margin: 0 0 15px 0;
	display: flex;
	align-items: center;
	gap: 8px;
}

.timebox-detail__count {
	background: var(--color-primary);
	color: white;
	font-size: 12px;
	padding: 2px 8px;
	border-radius: 10px;
	font-weight: normal;
}

.timebox-detail__empty-column {
	text-align: center;
	color: var(--color-text-maxcontrast);
	padding: 40px 20px;
	font-size: 14px;
	border: 2px dashed var(--color-border);
	border-radius: var(--border-radius-large);
}

.timebox-detail__source-section {
	margin-bottom: 20px;
}

.timebox-detail__source-heading {
	font-size: 14px;
	font-weight: 600;
	color: var(--color-text-maxcontrast);
	margin: 0 0 8px 0;
	padding-bottom: 4px;
	border-bottom: 1px solid var(--color-border);
}

.timebox-detail__source-list {
	list-style: none;
	padding: 0;
	margin: 0;
}

.timebox-detail__source-item {
	display: flex;
	align-items: center;
	padding: 8px 10px;
	cursor: pointer;
	border-radius: var(--border-radius);
	margin-bottom: 2px;
	gap: 8px;
	transition: background-color 0.2s;
}

.timebox-detail__source-item:hover {
	background-color: var(--color-background-hover);
}

.timebox-detail__source-item--task {
	border-left: 3px solid var(--color-success);
}

.timebox-detail__source-item--event {
	border-left: 3px solid var(--color-primary);
}

.timebox-detail__item-icon {
	font-size: 16px;
}

.timebox-detail__item-name {
	flex: 1;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	font-size: 14px;
}

.timebox-detail__item-date {
	font-size: 12px;
	color: var(--color-text-maxcontrast);
	white-space: nowrap;
	background-color: var(--color-background-dark);
	border-radius: var(--border-radius);
	padding: 1px 6px;
}

.timebox-detail__item-date--overdue {
	color: var(--color-error);
	font-weight: 600;
}

.timebox-detail__item-date--past {
	opacity: 0.6;
}

.timebox-detail__add-btn {
	background: none;
	border: 1px solid var(--color-border);
	border-radius: 50%;
	width: 24px;
	height: 24px;
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	font-size: 16px;
	color: var(--color-text-maxcontrast);
	transition: all 0.2s;
}

.timebox-detail__add-btn:hover {
	background-color: var(--color-primary);
	color: white;
	border-color: var(--color-primary);
}

.timebox-detail__no-items {
	color: var(--color-text-maxcontrast);
	font-size: 13px;
	font-style: italic;
	padding: 5px 0;
}

.timebox-detail__manual-add {
	display: flex;
	gap: 8px;
	align-items: center;
}

.timebox-detail__manual-input {
	flex: 1;
}

.timebox-detail__manual-select {
	width: auto;
	min-width: 80px;
}

@media (max-width: 768px) {
	.timebox-detail__content {
		flex-direction: column;
	}
}
</style>