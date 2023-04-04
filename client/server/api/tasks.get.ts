const config = useRuntimeConfig();
const baseURL = config.public.apiUrl;
const Referer = config.public.clientUrl;
export default defineEventHandler(async (event) => {
	const tokenResponse = async () => {
		await $fetch.raw('sanctum/csrf-cookie', {
			baseURL,
			method: 'GET',
			credentials: 'include',
			headers: {
				'Access-Control-Allow-Origin': Referer,
				Referer,
				Accept: 'application/json',
				'Content-Type': 'application/json'
			}
		});
	};
	let token = getCookie(event, 'X-XSRF-TOKEN')?.valueOf();
	if (!token) {
		await tokenResponse();
		token = getCookie(event, 'X-XSRF-TOKEN')?.valueOf();
	}
	event.context.csrf_cookie = token;
	console.log(token);
	if (token) {
		try {
			const tasks = await fetch('tasks', {
				method: 'GET',
				baseURL: baseURL,
				credentials: 'include',
				headers: {
					'Access-Control-Allow-Origin': Referer,
					Referer,
					Accept: 'application/json',
					'Content-Type': 'application/json',
					'X-XSRF-TOKEN': token
				} as HeadersInit
			});

			if (tasks) {
				return {
					status: 200,
					body: tasks
				};
			}
		} catch (error) {
			event.context.error = error;
		}
	}
});
