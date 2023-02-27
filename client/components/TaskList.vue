<script lang="ts" setup>
	import { Tasks, useTaskStore } from '~~/store/task';

	const taskStore = useTaskStore();
	taskStore.fetchTasks();

	defineProps<{
		taskItems: Tasks;
	}>();
</script>
<template>
	<div v-if="taskItems.length > 0">
		<TaskItem
			v-for="taskItem in taskItems"
			:key="taskItem.data.id"
			:task="taskItem"
		/>
	</div>
</template>
