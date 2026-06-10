<template>
	<div class="app-navigation">
		<slot name="list" />
	</div>
</template>

<script>
export default {
	name: 'AppNavigation',
}
</script>

<style scoped>
.app-navigation {
	width: 300px;
	min-width: 300px;
	height: 100%;
	background-color: var(--color-main-background);
	border-right: 1px solid var(--color-border);
	overflow-y: auto;
	position: relative;
}
</style>