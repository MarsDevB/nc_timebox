<template>
	<div class="sortable-list">
		<draggable
			:list="localItems"
			item-key="id"
			handle=".sortable-list__handle"
			ghost-class="sortable-list__ghost"
			animation="200"
			@end="onDragEnd"
		>
			<template #item="{ element, index }">
				<div
					class="sortable-list__item"
					:class="{
						'sortable-list__item--task': element.itemType === 'task',
						'sortable-list__item--event': element.itemType === 'event',
						'sortable-list__item--note': element.itemType === 'note',
					}"
				>
					<span class="sortable-list__handle" title="Drag to reorder">⠿</span>
					<span class="sortable-list__icon">
						<template v-if="element.itemType === 'task'">☑</template>
						<template v-else-if="element.itemType === 'event'">📅</template>
						<template v-else>📝</template>
					</span>
					<span class="sortable-list__title">{{ element.title }}</span>
					<span class="sortable-list__order">{{ index + 1 }}</span>
					<button
						class="sortable-list__remove"
						@click="$emit('delete', element.id)"
						title="Remove from timebox"
					>
						×
					</button>
				</div>
			</template>
		</draggable>
	</div>
</template>

<script>
import { ref, watch } from 'vue'
import draggable from 'vuedraggable'

export default {
	name: 'SortableList',
	components: {
		draggable,
	},
	props: {
		items: {
			type: Array,
			default: () => [],
		},
	},
	emits: ['reorder', 'delete'],
	setup(props, { emit }) {
		const localItems = ref([...props.items])

		watch(() => props.items, (newItems) => {
			localItems.value = [...newItems]
		}, { deep: true })

		function onDragEnd() {
			const itemIds = localItems.value.map(item => item.id)
			emit('reorder', itemIds)
		}

		return {
			localItems,
			onDragEnd,
		}
	},
}
</script>

<style scoped>
.sortable-list {
	min-height: 100px;
}

.sortable-list__item {
	display: flex;
	align-items: center;
	padding: 10px 12px;
	margin-bottom: 4px;
	background-color: var(--color-main-background);
	border: 1px solid var(--color-border);
	border-radius: var(--border-radius);
	gap: 8px;
	transition: box-shadow 0.2s, transform 0.2s;
}

.sortable-list__item:hover {
	box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.sortable-list__item--task {
	border-left: 4px solid var(--color-success);
}

.sortable-list__item--event {
	border-left: 4px solid var(--color-primary);
}

.sortable-list__item--note {
	border-left: 4px solid var(--color-warning);
}

.sortable-list__handle {
	cursor: grab;
	font-size: 18px;
	color: var(--color-text-maxcontrast);
	padding: 0 4px;
	user-select: none;
}

.sortable-list__handle:active {
	cursor: grabbing;
}

.sortable-list__icon {
	font-size: 16px;
	flex-shrink: 0;
}

.sortable-list__title {
	flex: 1;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	font-size: 14px;
}

.sortable-list__order {
	background: var(--color-background-dark);
	color: var(--color-text-maxcontrast);
	font-size: 11px;
	font-weight: bold;
	padding: 2px 7px;
	border-radius: 10px;
	flex-shrink: 0;
}

.sortable-list__remove {
	background: none;
	border: none;
	font-size: 18px;
	cursor: pointer;
	color: var(--color-text-maxcontrast);
	padding: 2px 6px;
	border-radius: var(--border-radius);
	opacity: 0;
	transition: opacity 0.2s;
}

.sortable-list__item:hover .sortable-list__remove {
	opacity: 1;
}

.sortable-list__remove:hover {
	background-color: var(--color-error);
	color: white;
}

.sortable-list__ghost {
	opacity: 0.5;
	background: var(--color-primary-light);
	border: 2px dashed var(--color-primary);
}
</style>