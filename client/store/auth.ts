import { defineStore } from 'pinia';

interface Auth {
	id: string;
	username: string;
}

interface AuthState {
	auth: Auth | undefined;
}

const state = (): AuthState => ({
	auth: undefined
});

const getters = {
	getAuth: (state: AuthState) => async () => {
		return state.auth;
	}
};

const actions = {
	async fetchAuth() {
		const { data }: any = await useApi('user', {
			pick: ['id', 'authname']
		});
		// @ts-ignore
		this.auth = { id: data.id, username: data.username };
	}
};

export const useAuthStore = defineStore('authStore', {
	state,
	getters,
	actions
});
