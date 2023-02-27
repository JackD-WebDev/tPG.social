<script lang="ts" setup>
	import { useTaskStore, Task } from '~~/store/task';

	const props = defineProps<{
		task: Task;
	}>();

	const task = ref(props.task);

	const taskStore = useTaskStore();

	const deleteTask = (id: string) => taskStore.deleteTask(id);

	const updateTask = (task: Task) => {
		const completionStatus = task.data.attributes.completed;
		taskStore.updateTask(task.data.id, { completed: !completionStatus });
	};
</script>

<template>
	<div>
		<pre>{{ task }}</pre>
		<h3
			:class="{ done: task.data.attributes.completed }"
			:title="task.data.attributes.title"
		>
			{{ task.data.attributes.title }}
		</h3>
		<p>
			{{ task.data.attributes.description }}
		</p>
		<p>
			{{ task.data.attributes.created_at_dates.created_at_human }}
		</p>
		<button @click="updateTask(task)">
			{{ task.data.attributes.completed ? 'UNDO' : 'DONE' }}
		</button>
		<button @click="deleteTask(task.data.id)">DELETE</button>
		<NuxtLink class="btn" :to="`/tasks/${task.data.id}`">EDIT</NuxtLink>
	</div>
</template>
