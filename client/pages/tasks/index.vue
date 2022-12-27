<script setup lang="ts">
	import { useTaskStore } from '~~/store/task';

	const config = useRuntimeConfig();
	console.log('Client URL:', config.CLIENT_URL);
	if (process.server) {
		console.log('API URL:', config.API_URL);
	}

	const taskStore = useTaskStore();
	// const newTask = ref('');
	const newTask = useState('newTask', () => {
		return '';
	});
	const error = useState('error', () => {
		return null;
	});

	const createNewTask = async () => {
		const apiBody = { title: newTask.value };

		if (newTask.value.length <= 0) {
			error.value = true;
			return;
		}

		const response = await useApi('tasks', {
			method: 'POST',
			body: JSON.stringify(apiBody)
		});

		console.log(response);

		taskStore.addTask(response);

		newTask.value = '';
	};

	error.value = false;
</script>
<template>
	<div>
		<h2>TASKS</h2>
		<TaskForm v-model="newTask" :error="error" @submit="createNewTask" />
		<TaskList :taskItems="taskStore.getOrderedTasks.reverse()" />
	</div>
</template>
