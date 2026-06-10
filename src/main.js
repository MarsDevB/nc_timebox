/**
 * Nextcloud - TimeBox
 *
 * Main entry point for the TimeBox app (Vue 3).
 */

import { createApp } from 'vue'
import App from './App.vue'
import { translate, translatePlural } from '@nextcloud/l10n'
import '../css/timebox.css'

const app = createApp(App)

app.config.globalProperties.t = translate
app.config.globalProperties.n = translatePlural

app.mount('#timebox-app')