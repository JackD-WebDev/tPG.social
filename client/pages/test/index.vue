<script setup lang="ts">
	const config = useRuntimeConfig();
	const baseURL = config.public.clientUrl;
	const test = ref('');

	const runTest = async () => {
		const data = await $fetch('/api/test', {
			baseURL
		});
		test.value = data;
	};
</script>

<template>
	<section>
		<h2>TEST</h2>
		<pre>{{ test }}</pre>
		<button @click="runTest">Run Test</button>
	</section>
</template>
