import { defineStore } from 'pinia';

export interface User {
	id: string;
	username: string;
}

interface UserState {
	user: User | undefined;
}

const state = (): UserState => ({
	user: undefined
});

const getters = {
	getUser: (state: UserState) => async () => {
		return state.user;
	}
};

const actions = {
	async fetchUser() {
		// @ts-ignore
		const { data } = await useApi('user', {
			pick: ['id', 'username']
		});
		this.user = { id: data.id, username: data.username };
	}
};

export const useUserStore = defineStore('userStore', {
	state,
	getters,
	actions
});
