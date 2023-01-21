interface UserInterface {
	data: {
		type: 'user';
		user_id: string;
		attributes: {
			username: string;
			email: string;
			cover_image: string;
			profile_image: string;
			tagline: string;
			about: string;
			formatted_address: string;
			location: string;
			created_at_dates: {
				created_at: string;
				created_at_human: string;
			};
			updated_at_dates: {
				updated_at: string;
				updated_at_human: string;
			};
		};
	};
	links: {
		client: string;
		api: string;
	};
}

class User implements UserInterface {
	data: {
		type: 'user';
		user_id: string;
		attributes: {
			username: string;
			email: string;
			cover_image: string;
			profile_image: string;
			tagline: string;
			about: string;
			formatted_address: string;
			location: string;
			created_at_dates: {
				created_at: string;
				created_at_human: string;
			};
			updated_at_dates: {
				updated_at: string;
				updated_at_human: string;
			};
		};
	};
	links: {
		client: string;
		api: string;
	};
	constructor(data: UserInterface) {
		this.data = data.data;
		this.links = data.links;
	}
}

export default User;
