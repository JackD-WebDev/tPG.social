import { FetchOptions } from 'unenv/runtime/fetch';

interface NitroFetchOptions<T> extends FetchOptions {
	method?:
		| 'DELETE'
		| 'GET'
		| 'HEAD'
		| 'PATCH'
		| 'POST'
		| 'PUT'
		| 'CONNECT'
		| 'OPTIONS'
		| 'TRACE';
	data?: T;
}

const config = useRuntimeConfig();
const baseURL = config.private.apiUrl;
const Referer = config.public.clientUrl;
let session = '';

export default defineEventHandler((event) => {
	const apiRequest = async <T>(url: string, options?: NitroFetchOptions<T>) => {
		session = getCookie(event, 'tpg_api_session')?.valueOf() ?? session;

		let csrfToken =
			getCookie(event, 'XSRF-TOKEN')?.valueOf() ?? event.context.csrf;

		const headers: HeadersInit = {
			Referer,
			Accept: 'application/json',
			'Content-Type': 'application/json',
			'Access-Control-Allow-Origin': Referer,
			'X-XSRF-TOKEN': csrfToken,
			Cookie: `XSRF-TOKEN=${csrfToken}; ${
				session ? `tpg_api_session=${session}` : ''
			}`,
			...(options?.headers || {})
		};

		const opts: NitroFetchOptions<string> = options
			? (({ headers, ...opts }) => opts)(options as NitroFetchOptions<string>)
			: { method: 'GET' };

		return $fetch(url, {
			credentials: 'include',
			baseURL,
			headers,
			...opts
		});
	};

	event.context.apiRequest = apiRequest;
});
