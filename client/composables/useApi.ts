import { FetchOptions } from 'unenv/runtime/fetch';

let baseURL = process.env
	? (process.env.API_URL as string)
	: 'http://localhost:8000/api/';
let Referer = window.location
	? window.location.origin
	: 'http://localhost:3000';
let csrf_cookie = 'XSRF-TOKEN';

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

export const useApi = async (url: string, options?: FetchOptions) => {
	let token = useCookie(csrf_cookie)?.value;
	if (!token) {
		await tokenResponse();
		token = useCookie(csrf_cookie).value;
	}

	const headers: HeadersInit =
		{
			Referer,
			Accept: 'application/json',
			'Content-Type': 'application/json',
			'X-XSRF-TOKEN': token ?? '',
			...options?.headers
		} ?? {};

	const opts: FetchOptions = options
		? (({ headers, ...opts }) => opts)(options)
		: null ?? {};

	return $fetch(url, {
		credentials: 'include',
		baseURL,
		headers,
		...opts
	});
};
