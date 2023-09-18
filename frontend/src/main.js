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

const app = createApp(App)
    .component('MyHeader', MyHeader)
    .component('MyTypeText', MyTypeText)
    .component('TextInput', TextInput)
    .component('InputItem', InputItem)
    .component('MySelect', MySelect)
    .component('MyLoading', MyLoading)
    .component('ProgressBar', ProgressBar)

app.use(createPinia())
app.use(router)

app.mount('#app')
