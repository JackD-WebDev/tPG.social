import { FetchOptions } from 'unenv/runtime/fetch';

interface NitroFetchOptions<T> extends FetchOptions {
	method?:
		| 'delete'
		| 'get'
		| 'GET'
		| 'head'
		| 'HEAD'
		| 'PATCH'
		| 'post'
		| 'POST'
		| 'put'
		| 'PUT'
		| 'connect'
		| 'CONNECT'
		| 'OPTIONS'
		| 'options'
		| 'TRACE'
		| 'trace';
	data?: T;
}

export const useApi = async <T>(
	url: string,
	options?: NitroFetchOptions<T>
) => {
	let baseURL = 'http://localhost:8000/api/';
	let Referer = 'http://localhost:3000';
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

	let token = useCookie(csrf_cookie)?.value;
	if (!token) {
		await tokenResponse();
		token = useCookie(csrf_cookie)?.value;
	}

	const headers: HeadersInit = {
		Referer,
		Accept: 'application/json',
		'Content-Type': 'application/json',
		'X-XSRF-TOKEN': token ?? '',
		...(options?.headers || {})
	};

	const opts: NitroFetchOptions<string> = options
		? (({ headers, ...opts }) => opts)(options as NitroFetchOptions<string>)
		: { method: 'GET' };

	return $fetch(url, {
		credentials: 'include',
		baseURL,
		...opts
	});
};
