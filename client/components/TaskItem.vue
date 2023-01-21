<script lang="ts" setup>
	import { useTaskStore } from '~~/store/task';
	import { Task } from '~~/store/task';

	const props = defineProps<{
		task: Task;
	}>();

	const task = ref(props.task);

	const taskStore = useTaskStore();
	const deleteTask = (id: string) => taskStore.removeTask(id);

	const updateTask = (task: Task) => {
		const completionStatus = task.completed;
		taskStore.updateTask(task.id, { completed: !completionStatus });
	};
</script>

<template>
	<div>
		<pre>{{ task }}</pre>
		<h3 :class="{ done: task.completed }" :title="task.title">
			{{ task.title }}
		</h3>
		<p>
			{{ task.description }}
		</p>
		<p>
			{{ task.createdAtHuman }}
		</p>
		<button @click="updateTask(task)">
			{{ task.completed ? 'UNDO' : 'DONE' }}
		</button>
		<button @click="deleteTask(task.id)">DELETE</button>
		<NuxtLink class="btn" :to="`/tasks/${task.id}`">EDIT</NuxtLink>
	</div>
</template>
