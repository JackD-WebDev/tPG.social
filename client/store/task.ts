import { defineStore } from 'pinia';

export interface Task {
	id: string;
	title: string;
	description?: string;
	type?: string;
	priority?: string;
	location?: string;
	notes?: string;
	completed?: boolean;
	createdAt?: string;
	createdAtHuman?: string;
	updatedAt?: string;
	updatedAtHuman?: string;
	link?: string;
}

export type Tasks = Task[] | [];

interface TaskState {
	tasks: Tasks;
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

const state = (): TaskState => ({
	tasks: []
});

const getters = {
	getTaskById: (state: TaskState) => (id: string) => {
		return state.tasks.find((task) => !!task && (task as Task).id === id);
	},
	getOrderedTasks: (state: TaskState) =>
		[...state.tasks].sort(
			// sort by completed, then by priority, then by date
			(a, b) => {
				if (a.completed !== b.completed) {
					return a.completed ? 1 : -1;
				} else if (a.priority !== b.priority) {
					return a.priority === 'high' ? -1 : 1;
				} else {
					return a.createdAt > b.createdAt ? -1 : 1;
				}
			}
		)
};

const actions = {
	async addTask(taskData: Task) {
		const newTask: Task = {
			id: taskData.data.task_id,
			title: taskData.data.attributes.title,
			description: taskData.data.attributes.description,
			type: taskData.data.attributes.task_type,
			priority: taskData.data.attributes.priority,
			location: taskData.data.attributes.location,
			notes: taskData.data.attributes.notes,
			completed: false,
			createdAt: taskData.data.attributes.updated_at_dates.created_at,
			createdAtHuman:
				taskData.data.attributes.created_at_dates.created_at_human,
			updatedAt: taskData.data.attributes.updated_at_dates.updated_at,
			updatedAtHuman:
				taskData.data.attributes.updated_at_dates.updated_at_human,
			link: taskData.links.client
		};

		await this.tasks.push(newTask);
	},

	async removeTask(id: string) {
		this.tasks = this.tasks.filter((task: Task) => task.id !== id);
	},
	async updateTask(id: string, updateTask: UpdateTask) {
		const index = this.tasks.findIndex((task) => task.id === id);
		this.tasks[index] = {
			...this.tasks[index],
			...updateTask,
			updatedAt: new Date()
		};
	}
};

export const useTaskStore = defineStore('taskStore', {
	state,
	getters,
	actions
});
