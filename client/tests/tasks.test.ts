import { setActivePinia, createPinia } from "pinia"
import { useTaskStore } from "../store/task"
import { describe, it, expect, beforeAll, beforeEach, afterEach } from "vitest"

const getFirstTaskId = (store: ReturnType<typeof useTaskStore>) => {
	return store.tasks[0].id
}

beforeAll(() => {
	setActivePinia(createPinia())
})

describe("useTaskStore", () => {
	let store: ReturnType<typeof useTaskStore>

	beforeEach(() => {
		store = useTaskStore()
	})

	afterEach(() => {
		store.$reset()
	})

	it("creates a store", () => {
		expect(store).toBeDefined()
	})

	it("creates a feature task", () => {
		store.addTask({ label: "test" })

		expect(store.tasks).toStrictEqual([
			{
				id: expect.any(String),
				label: "test",
				description: "",
				completed: false,
				createdAt: expect.any(Date),
				updatedAt: expect.any(Date)
			}
		])
	})

	it("fetches a task by id", () => {
		store.addTask({ label: "test" })
		const id = getFirstTaskId(store)
		const task = store.getTaskById(id)
		expect(task.label).toBe("test")
	})

	it("retrieves tasks in order, without manipulating initial state", () => {
		const tasks = [
			{ createdAt: new Date(2021, 3, 7) },
			{ createdAt: new Date(2019, 3, 7) },
			{ createdAt: new Date(2020, 3, 7) }
		]

		// @ts-ignore
		store.tasks = tasks
		const orderedTasks = store.getOrderedTasks

		expect(orderedTasks[0].createdAt.getFullYear()).toBe(2019)
		expect(orderedTasks[1].createdAt.getFullYear()).toBe(2020)
		expect(orderedTasks[2].createdAt.getFullYear()).toBe(2021)
		expect(store.tasks[0].createdAt.getFullYear()).toBe(2021)
	})

	it("deletes a task", () => {
		store.addTask({ label: "test" })
		const id = getFirstTaskId(store)
		store.removeTask(id)
		expect(store.tasks).toStrictEqual([])
	})

	it("updates a task's completed state", () => {
		store.addTask({ label: "test" })
		const id = getFirstTaskId(store)
		store.updateTask(id, { completed: true })
		expect(store.getTaskById(id).completed).toBe(true)
	})

	it("updates a task's label", () => {
		store.addTask({ label: "test" })
		const id = getFirstTaskId(store)
		store.updateTask(id, { label: "test2" })
		expect(store.getTaskById(id).label).toBe("test2")
	})
})
