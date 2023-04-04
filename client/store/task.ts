import { defineStore } from 'pinia';
import { z } from 'zod';

export const taskSchema = z.object({
	data: z.object({
		task_id: z.string(),
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
		task_id: string;
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
export interface UpdateTask {
	title?: string;
	description?: string;
	type?: string;
	priority?: string;
	location?: string;
	notes?: string;
	completed?: boolean;
}

export const newTaskSchema = z.object({
	title: z.string()
});

const state = (): TaskState => ({
	tasks: []
});

const getters = {
	getTasks(state: TaskState) {
		return state.tasks;
	},

	getTaskById: (state: TaskState) => (task_id: string) => {
		return state.tasks.find(
			(task) => !!task && (task as Task).data.task_id === task_id
		);
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
			// const collection = await $fetch('localhost:3000/api/tasks');
			const result = collectionSchema.parse(collection);
			this.$patch({ tasks: result.data });
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
			return result;
		} catch (error) {
			console.error(error);
		}
	},

	async updateTask(task_id: string, updatedTask: UpdateTask) {
		const response = await useApi(`tasks/${task_id}`, {
			method: 'PUT',
			body: JSON.stringify(updatedTask)
		});

		try {
			const result = responseSchema.parse(response);
			this.tasks.unshift(result.data);
			return result;
		} catch (error) {
			console.error(error);
		}
	},

	async deleteTask(task_id: string) {
		const response = await useApi(`tasks/${task_id}`, {
			method: 'DELETE'
		});

		try {
			const result = responseSchema.parse(response);
			this.tasks = this.tasks.filter(
				(task: Task) => (task as Task).data.task_id !== task_id
			);
			return result;
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
