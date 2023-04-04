<script setup lang="ts">
	import { useTaskStore, NewTask, Tasks } from '~~/store/task';
	import { storeToRefs } from 'pinia';

	const taskStore = useTaskStore();
	const { getOrderedTasks } = taskStore;
	const { tasks } = storeToRefs(taskStore);

	const newTask = ref<NewTask>({
		title: ''
	});

	const error = ref('');

	onMounted(() => {
		taskStore.fetchTasks();
	});
</script>

<template>
	<section>
		<h2>TASKS</h2>
		<p v-if="error">Error: {{ error }}</p>
		<TaskForm
			v-model="newTask.title"
			:error="false"
			@submit="taskStore.createTask(newTask)"
		/>
		<TaskList :taskItems="tasks" />
	</section>
</template>
