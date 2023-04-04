export default defineEventHandler(async (event) => {
	try {
		return await event.context.apiRequest('tasks');
	} catch (error) {
		console.log(error);
	}
});
