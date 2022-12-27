<script lang="ts" setup>
	import { useUserStore } from '~~/store/user';

	const userStore = useUserStore();

	const sessionCookie = useCookie('tpg_api_session');

	let userData: any = ref('');

	const user = computed(() => {
		return userStore.user;
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
		userData.value = response;
		userStore.fetchUser();
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
		userData.value = response;
		userStore.$reset();
	}
</script>

<template>
	<div>
		<button @click="login">login</button>
		<button @click="logout">logout</button>

		<pre>{{ userData.message }}</pre>
		<p v-if="userData.data">Welcome {{ userData.data.username }}</p>
		<p v-if="user">{{ user }}</p>

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
