<script lang="ts" setup>
	import { Tasks, useTaskStore } from '~~/store/task';

	const taskStore = useTaskStore();
	taskStore.fetchTasks();

	defineProps<{
		taskItems: Tasks;
	}>();
</script>
<template>
	<ul v-if="taskItems.length > 0">
		<TaskItem
			v-for="taskItem in taskItems"
			:key="taskItem.data.task_id"
			:task="taskItem"
		/>
	</ul>
</template>
