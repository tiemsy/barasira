import { createI18n } from 'vue-i18n'
import fr from './locales/fr.json'
import en from './locales/en.json'
import bm from './locales/bm.json'

const requestedLocale = (localStorage.getItem('lang') || navigator.language || 'fr')
  .toLowerCase()
  .split(/[-_]/)[0]
const locale = ['fr', 'en', 'bm'].includes(requestedLocale) ? requestedLocale : 'fr'

export default createI18n({
  legacy: false,
  locale,
  fallbackLocale: 'fr',
  messages: { fr, en, bm }
})
