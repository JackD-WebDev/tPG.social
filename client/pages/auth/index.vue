<script lang="ts" setup>
	import { useAuthStore } from '~~/store/auth';

	const authStore = useAuthStore();

	const sessionCookie = useCookie('tpg_api_session');

	let authData: any = ref('');

	const auth = computed(() => {
		return authStore.auth;
	});

	const login = async () => {
		// @ts-ignore
		const response = await useApi('login', {
			method: 'POST',
			body: {
				username: 'jack',
				password: 'aaaaaaaa'
			}
		});
		authData.value = response;
		authStore.fetchAuth();
	};

	async function logout() {
		//@ts-ignore
		const response = await useApi('logout', {
			method: 'POST'
		});
		useCookie('tpg_api_session', {
			expires: new Date(0)
		});
		sessionCookie.value = null;
		authData.value = response;
		authStore.$reset();
	}
</script>

<template>
	<div>
		<button @click="login">LOGIN</button>
		<button @click="logout">LOGOUT</button>

		<pre>{{ authData.message }}</pre>
		<p v-if="authData.data">Welcome {{ authData.data.username }}</p>
		<p v-if="auth">{{ auth }}</p>

		<!-- <h1>
			{{ isLoggedIn ? 'LOGGED IN' : 'LOGGED OUT' }}
		</h1>
		<form @submit.prevent="onSubmit">
			<input
				v-model="username"
				type="text"
				placeholder="username"
				name="username"
				id="username"
			/>
			<input
				v-model="password"
				type="password"
				placeholder="Password"
				name="password"
				id="password"
			/>
		</form>
		<button v-if="!isLoggedIn" :disabled="isLoading" type="submit">
			LOGIN
		</button>
		<button v-if="isLoggedIn" :disabled="isLoading" @click="logout">
			LOGOUT
		</button>
		<p v-if="error">{{ error }}</p> -->
	</div>
</template>
