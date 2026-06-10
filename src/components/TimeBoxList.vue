<template>
	<div class="timebox-list">
		<div class="timebox-list__header">
			<h2>TimeBoxes</h2>
			<button
				class="timebox-list__add icon-add"
				@click="showCreateForm = !showCreateForm"
				:title="'Create new timebox'"
			>
				+
			</button>
		</div>

		<!-- Create form -->
		<div v-if="showCreateForm" class="timebox-list__create-form">
			<input
				v-model="newTitle"
				type="text"
				placeholder="TimeBox name..."
				class="timebox-list__input"
				@keyup.enter="createTimebox"
				ref="titleInput"
			/>
			<input
				v-model="newColor"
				type="color"
				class="timebox-list__color-picker"
			/>
			<div class="timebox-list__create-actions">
				<button class="primary" @click="createTimebox">Create</button>
				<button @click="cancelCreate">Cancel</button>
			</div>
		</div>

		<!-- Timebox items -->
		<ul class="timebox-list__items">
			<li
				v-for="tb in timeboxes"
				:key="tb.id"
				class="timebox-list__item"
				:class="{ 'timebox-list__item--active': tb.id === activeId }"
				@click="$emit('select', tb.id)"
			>
				<span class="timebox-list__color-dot" :style="{ backgroundColor: tb.color }"></span>
				<span class="timebox-list__item-title">{{ tb.title }}</span>
				<button
					class="timebox-list__delete icon-delete"
					@click.stop="confirmDelete(tb.id)"
					title="Delete timebox"
				>
					×
				</button>
			</li>
		</ul>

		<div v-if="timeboxes.length === 0 && !showCreateForm" class="timebox-list__empty">
			<p>No timeboxes yet. Create one to get started!</p>
		</div>
	</div>
</template>

<script>
import { ref, nextTick } from 'vue'

export default {
	name: 'TimeBoxList',
	props: {
		timeboxes: {
			type: Array,
			default: () => [],
		},
		activeId: {
			type: Number,
			default: null,
		},
	},
	emits: ['select', 'create', 'delete'],
	setup(props, { emit }) {
		const showCreateForm = ref(false)
		const newTitle = ref('')
		const newColor = ref('#0082c9')
		const titleInput = ref(null)

		async function createTimebox() {
			if (!newTitle.value.trim()) return
			emit('create', {
				title: newTitle.value.trim(),
				color: newColor.value,
			})
			newTitle.value = ''
			newColor.value = '#0082c9'
			showCreateForm.value = false
		}

		function cancelCreate() {
			showCreateForm.value = false
			newTitle.value = ''
			newColor.value = '#0082c9'
		}

		function confirmDelete(id) {
			if (confirm('Are you sure you want to delete this timebox?')) {
				emit('delete', id)
			}
		}

		return {
			showCreateForm,
			newTitle,
			newColor,
			titleInput,
			createTimebox,
			cancelCreate,
			confirmDelete,
		}
	},
}
</script>

<style scoped>
.timebox-list {
	padding: 10px;
}

.timebox-list__header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	padding: 10px 5px;
}

.timebox-list__header h2 {
	font-size: 18px;
	font-weight: bold;
	margin: 0;
	color: var(--color-text-light);
}

.timebox-list__add {
	background: none;
	border: none;
	font-size: 22px;
	cursor: pointer;
	color: var(--color-text-maxcontrast);
	padding: 5px 10px;
	border-radius: var(--border-radius);
}

.timebox-list__add:hover {
	background-color: var(--color-background-hover);
}

.timebox-list__create-form {
	padding: 10px;
	background: var(--color-background-dark);
	border-radius: var(--border-radius);
	margin-bottom: 10px;
}

.timebox-list__input {
	width: 100%;
	margin-bottom: 8px;
}

.timebox-list__color-picker {
	width: 40px;
	height: 30px;
	border: none;
	padding: 0;
	cursor: pointer;
	margin-bottom: 8px;
}

.timebox-list__create-actions {
	display: flex;
	gap: 8px;
}

.timebox-list__create-actions button {
	padding: 5px 15px;
	border-radius: var(--border-radius);
	cursor: pointer;
}

.timebox-list__items {
	list-style: none;
	padding: 0;
	margin: 0;
}

.timebox-list__item {
	display: flex;
	align-items: center;
	padding: 8px 10px;
	cursor: pointer;
	border-radius: var(--border-radius);
	margin-bottom: 2px;
	gap: 8px;
}

.timebox-list__item:hover {
	background-color: var(--color-background-hover);
}

.timebox-list__item--active {
	background-color: var(--color-primary-light);
}

.timebox-list__color-dot {
	width: 12px;
	height: 12px;
	border-radius: 50%;
	flex-shrink: 0;
}

.timebox-list__item-title {
	flex: 1;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	font-size: 14px;
}

.timebox-list__delete {
	background: none;
	border: none;
	color: var(--color-text-maxcontrast);
	cursor: pointer;
	font-size: 16px;
	padding: 2px 6px;
	border-radius: var(--border-radius);
	opacity: 0;
	transition: opacity 0.2s;
}

.timebox-list__item:hover .timebox-list__delete {
	opacity: 1;
}

.timebox-list__delete:hover {
	background-color: var(--color-error);
	color: white;
}

.timebox-list__empty {
	text-align: center;
	color: var(--color-text-maxcontrast);
	padding: 20px 10px;
	font-size: 14px;
}
</style>