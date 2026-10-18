import {createApp} from 'vue';

import {translate, translatePlural} from '@nextcloud/l10n';

import App from './App.vue';

const app = createApp(App);
app.config.globalProperties.t = translate;
app.config.globalProperties.n = translatePlural;
app.config.globalProperties.OC = window.OC;
app.config.globalProperties.OCA = window.OCA;

app.mount("#cas-settings");

export default app;
