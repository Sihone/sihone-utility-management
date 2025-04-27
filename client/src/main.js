import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

// Import Vuetify
import { createVuetify } from 'vuetify'
import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css' // For icons

import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'


const vuetify = createVuetify()

// Import Tailwind if you still want it
import './assets/main.css'
import './style.css'

const app = createApp(App)

app.use(router)
app.use(vuetify)
app.use(Toast)

app.mount('#app')
