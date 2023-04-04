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
		taskStore.updateTask(task.data.task_id, { completed: !completionStatus });
	};
</script>

<template>
	<li>
		<h3
			:class="{ done: task.data.attributes.completed }"
			:title="task.data.attributes.title"
		>
			{{ task.data.attributes.title.toUpperCase() }}
		</h3>
		<p v-if="task.data.attributes.description != null">
			{{ task.data.attributes.description }}
		</p>
		<p>Created: {{ task.data.attributes.created_at_dates.created_at_human }}</p>
		<p>Updated: {{ task.data.attributes.updated_at_dates.updated_at_human }}</p>
		<p>Priority: {{ task.data.attributes.priority }}</p>
		<p>Type: {{ task.data.attributes.task_type }}</p>
		<p>Location: {{ task.data.attributes.location }}</p>
		<p v-if="task.data.attributes.notes != null">
			Notes: {{ task.data.attributes.notes }}
		</p>
		<button class="btn" @click="updateTask(task)">
			{{ task.data.attributes.completed ? 'UNDO' : 'DONE' }}
		</button>
		<button class="btn" @click="deleteTask(task.data.task_id)">DELETE</button>
		<NuxtLink class="btn" :to="`/tasks/${task.data.task_id}`">EDIT</NuxtLink>
	</li>
</template>

<style scoped lang="scss">
	h3 {
		font-weight: 700;
		font-size: 2rem;
	}
	li {
		padding: 5rem;
		border: 0.5rem solid var(--primary-color);
		border-radius: 3rem;
		margin: 2rem 2rem;
		display: inline-block;
	}
</style>
