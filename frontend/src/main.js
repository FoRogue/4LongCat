import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createVuetify } from 'vuetify';
import 'vuetify/styles'; // Подключение стилей Vuetify

// Импорт всех компонентов Vuetify
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';

import App from './App.vue';
import router from './router';

const app = createApp(App);

// Создаём Vuetify с полной поддержкой компонентов и директив
const vuetify = createVuetify({
  components,
  directives
});

app.use(createPinia());
app.use(router);
app.use(vuetify);

app.mount('#app');
