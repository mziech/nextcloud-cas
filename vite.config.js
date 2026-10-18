import { createAppConfig } from '@nextcloud/vite-config';

export default createAppConfig({
	settings: 'src/settings.js',
	login: 'src/login.js'
});
