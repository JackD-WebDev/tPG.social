<script lang="ts" setup>
	const props = defineProps<{
		modelValue: string;
		error: boolean;
	}>();

	const emit = defineEmits<{
		(e: 'update:modelValue', value: string): void;
		(e: 'submit'): void;
	}>();

	const localState = computed({
		get() {
			return props.modelValue;
		},
		set(value) {
			emit('update:modelValue', value);
		}
	});
</script>

<template>
	<div>
		<input
			v-model="localState"
			@keypress.enter="$emit('submit')"
			type="text"
			placeholder="TASK NAME"
		/>
		<button @click="$emit('submit')">ADD TASK</button>
	</div>
</template>
