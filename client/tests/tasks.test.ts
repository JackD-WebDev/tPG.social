import { setActivePinia, createPinia } from 'pinia';
import { useTaskStore } from '../store/task';
import { describe, it, expect, beforeAll, beforeEach, afterEach } from 'vitest';

const getFirstTaskId = (store: ReturnType<typeof useTaskStore>) => {
	return store.tasks[0].data.id;
};

beforeAll(() => {
	setActivePinia(createPinia());
});

describe('useTaskStore', () => {
	let store: ReturnType<typeof useTaskStore>;

	beforeEach(() => {
		store = useTaskStore();
	});

	afterEach(() => {
		store.$reset();
	});

	it('creates a store', () => {
		expect(store).toBeDefined();
	});

	it('creates a feature task', () => {
		store.createTask({ title: 'test' });

		expect(store.tasks).toStrictEqual([
			{
				data: {
					id: expect.any(String),
					type: 'task',
					attributes: {
						title: 'test',
						completed: false
					}
				}
			}
		]);
	});

	it('fetches a task by id', () => {
		store.tasks = [
			{
				data: {
					id: '1',
					type: 'task',
					attributes: {
						title: 'test',
						description: 'test',
						task_type: 'feature',
						priority: 'low',
						location: 'test',
						notes: 'test',
						completed: false,
						created_at_dates: {
							created_at: new Date().toISOString(),
							created_at_human: new Date().toISOString()
						},
						updated_at_dates: {
							updated_at: new Date().toISOString(),
							updated_at_human: new Date().toISOString()
						}
					}
				}
			}
		];

		store.createTask({ title: 'test' });
		const id = getFirstTaskId(store);
		let task: ReturnType<typeof store.getTaskById>;
		if (id) {
			task = store.getTaskById(id);
		}
		expect(task?.data.attributes.title).toBe('test');
	});

	it('deletes a task', () => {
		store.createTask({ title: 'test' });
		const id = getFirstTaskId(store);
		store.deleteTask(id);
		expect(store.tasks).toStrictEqual([]);
	});

	it("updates a task's completed state", () => {
		store.createTask({ title: 'test' });
		const id = getFirstTaskId(store);
		store.updateTask(id, { completed: true });
		expect(store.getTaskById(id)?.data.attributes.completed).toBe(true);
	});

	it("updates a task's title", () => {
		store.createTask({ title: 'test' });
		const id = getFirstTaskId(store);
		store.updateTask(id, { title: 'test2' });
		expect(store.getTaskById(id)?.data.attributes.title).toBe('test2');
	});
});
