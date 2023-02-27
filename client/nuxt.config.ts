// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
	css: ['@/assets/styles/main.scss'],
	modules: ['@pinia/nuxt'],
	nitro: {
		externals: {
			inline: ['uuid']
		}
	},
	runtimeConfig: {
		public: {
			apiUrl: process.env.API_URL,
			clientUrl: process.env.CLIENT_URL
		}
	}
});
