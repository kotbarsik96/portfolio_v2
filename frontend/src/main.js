import './assets/style/style.scss'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

import MyHeader from './components/global/MyHeader.vue'
import MyTypeText from './components/global/MyTypeText.vue'
import TextInput from './components/global/TextInput.vue'
import InputItem from './components/global/InputItem.vue'
import MySelect from './components/global/MySelect.vue'
import MyLoading from './components/global/MyLoading.vue'
import ProgressBar from './components/private/ProgressBar.vue'

const components = [
    MyHeader,
    MyTypeText,
    TextInput,
    InputItem,
    MySelect,
    MyLoading,
    ProgressBar
]

const app = createApp(App)
components.forEach(comp => app.component(comp.name, comp))

app.use(createPinia())
app.use(router)

app.mount('#app')
