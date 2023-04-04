const config = useRuntimeConfig();
let baseURL = config.private.apiUrl;
let Referer = config.public.clientUrl;

export default defineEventHandler(async (event) => {
	let csrfToken = getCookie(event, 'XSRF-TOKEN')?.valueOf();

	const tokenResponse = async () => {
		await $fetch.raw('sanctum/csrf-cookie', {
			baseURL,
			method: 'GET',
			credentials: 'include',
			headers: {
				Referer,
				Accept: 'application/json',
				'Content-Type': 'application/json',
				'Access-Control-Allow-Origin': Referer
			}
		});
	};

	if (!csrfToken) {
		await tokenResponse();
		csrfToken = getCookie(event, 'XSRF-TOKEN')?.valueOf();
	}

	event.context.csrf = csrfToken;
});
