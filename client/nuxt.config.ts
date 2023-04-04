// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
	css: ['@/assets/styles/main.scss'],
	modules: ['@vueuse/nuxt', '@pinia/nuxt', '@nuxt/devtools'],
	nitro: {
		externals: {
			inline: ['uuid']
		}
	},
	runtimeConfig: {
		public: {
			clientUrl: process.env.CLIENT_URL
		},
		private: {
			apiUrl: process.env.API_URL
		}
	}
});
