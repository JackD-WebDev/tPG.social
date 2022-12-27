<script lang="ts" setup>
	import { Tasks } from "~~/store/task"

	defineProps<{
		taskItems: Tasks
	}>()
</script>
<template>
	<div v-if="taskItems.length > 0">
		<TaskItem
			v-for="taskItem in taskItems"
			:key="taskItem.id"
			:task="taskItem"
		/>
	</div>
</template>
