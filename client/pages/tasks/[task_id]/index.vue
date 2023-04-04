<script setup lang="ts">
	const route = useRoute();
	const routeId = computed(() => route.params.task_id);
</script>
<template>
	<p>Route {{ routeId }}</p>
</template>
