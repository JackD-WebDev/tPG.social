<script setup lang="ts">
	const route = useRoute();
	const routeId = computed(() => route.params.id);
</script>
<template>
	<p>{{ routeId }}</p>
</template>
