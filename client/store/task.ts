import { defineStore } from 'pinia';
import { z } from 'zod';

export const taskSchema = z.object({
	data: z.object({
		id: z.string(),
		type: z.string(),
		attributes: z.object({
			title: z.string(),
			description: z.string().nullable(),
			task_type: z.string(),
			priority: z.string(),
			location: z.string(),
			notes: z.string().nullable(),
			completed: z.number(),
			created_at_dates: z.object({
				created_at_human: z.string(),
				created_at: z.string()
			}),
			updated_at_dates: z.object({
				updated_at_human: z.string(),
				updated_at: z.string()
			})
		})
	})
});

export const responseSchema = z.object({
	success: z.boolean(),
	message: z.string(),
	data: z.array(taskSchema),
	links: z.object({
		self: z.string(),
		client: z.string()
	})
});

export const collectionSchema = z.object({
	success: z.boolean(),
	message: z.string(),
	data: z.array(taskSchema),
	links: z.object({
		self: z.string(),
		client: z.string()
	})
});

export interface Task {
	data: {
		id: string;
		type: 'task';
		attributes: {
			title: string;
			description: string | null;
			task_type: string;
			priority: string;
			location: string;
			notes: string | null;
			completed: boolean;
			created_at_dates: {
				created_at_human: string;
				created_at: string;
			};
			updated_at_dates: {
				updated_at_human: string;
				updated_at: string;
			};
		};
	};
}

export type Tasks = Task[] | [];
interface TaskState {
	tasks: Tasks;
}

export interface NewTask {
	title: string;
}

export const newTaskSchema = z.object({
	title: z.string()
});

export interface UpdateTask {
	title?: string;
	description?: string;
	type?: string;
	priority?: string;
	location?: string;
	notes?: string;
	completed?: boolean;
}

const state = (): TaskState => ({
	tasks: []
});

const getters = {
	getTasks(state: TaskState) {
		return state.tasks;
	},
	getTaskById: (state: TaskState) => (id: string) => {
		return state.tasks.find((task) => !!task && (task as Task).data.id === id);
	},
	getOrderedTasks: (state: TaskState) => {
		return [...state.tasks].sort(
			// sort by completed, then by priority, then by date
			(a, b) => {
				if (a.data.attributes.completed !== b.data.attributes.completed) {
					return a.data.attributes.completed ? 1 : -1;
				} else if (a.data.attributes.priority !== b.data.attributes.priority) {
					return a.data.attributes.priority === 'high' ? -1 : 1;
				} else {
					return a.data.attributes.created_at_dates.created_at >
						b.data.attributes.created_at_dates.created_at
						? -1
						: 1;
				}
			}
		);
	}
};

const actions = {
	async fetchTasks() {
		try {
			const collection = await useApi('tasks', { method: 'GET' });
			const result = collectionSchema.parse(collection);
			this.tasks = result.data;
		} catch (error) {
			console.error(error);
		}
	},

	async createTask(newTask: NewTask) {
		const response = await useApi('tasks', {
			method: 'POST',
			body: JSON.stringify({ title: newTask.title })
		});

		try {
			const result = responseSchema.parse(response);
			this.tasks.unshift(result.data);
			newTask.title = '';
		} catch (error) {
			console.error(error);
		}
	},

	async updateTask(id: string, updatedTask: UpdateTask) {
		const response = await useApi(`tasks/${id}`, {
			method: 'PUT',
			body: JSON.stringify(updatedTask)
		});

		try {
			const result = responseSchema.parse(response);
			this.tasks.unshift(result.data);
		} catch (error) {
			console.error(error);
		}
	},

	async deleteTask(id: string) {
		const response = await useApi(`tasks/${id}`, {
			method: 'DELETE'
		});

		try {
			const result = responseSchema.parse(response);
			this.tasks = this.tasks.filter((task) => (task as Task).data.id !== id);
		} catch (error) {
			console.error(error);
		}
	}
};

export const useTaskStore = defineStore('taskStore', {
	state,
	getters,
	actions
});
