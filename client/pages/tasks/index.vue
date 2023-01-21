<script setup lang="ts">
	import { useTaskStore } from '~~/store/task';

	const tasks = async () => {
		const response = await useApi('tasks', { method: 'GET' });
		console.log('response', response);
		return response;
	};
	tasks();

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
