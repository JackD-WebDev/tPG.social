import { FetchOptions } from 'unenv/runtime/fetch';

const baseURL: string = 'http://localhost:8000/api/';
const Referer: string = 'http://localhost:3000';
const csrf_cookie: string = 'XSRF-TOKEN';

const tokenResponse = async () => {
	await $fetch.raw('sanctum/csrf-cookie', {
		baseURL,
		method: 'GET',
		credentials: 'include',

		headers: {
			'Access-Control-Allow-Origin': '*',
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
