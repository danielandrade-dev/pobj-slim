import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
// Importa Tabler Icons localmente para garantir que os ícones funcionem
import '@tabler/icons-webfont/dist/tabler-icons.min.css'

const app = createApp(App)

app.use(router)

app.mount('#app')
