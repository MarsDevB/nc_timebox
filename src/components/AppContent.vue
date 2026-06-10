<template>
	<div class="app-content">
		<slot />
	</div>
</template>

<script>
export default {
	name: 'AppContent',
}
</script>

<style scoped>
.app-content {
	flex: 1 1 auto;
	height: 100%;
	overflow-y: auto;
}
</style>