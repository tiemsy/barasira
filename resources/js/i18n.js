import { createI18n } from 'vue-i18n'
import fr from './locales/fr.json'
import en from './locales/en.json'
import bm from './locales/bm.json'

export default createI18n({
  legacy: false,
  locale: localStorage.getItem("lang") || navigator.language.slice(0, 2) || "fr",
  fallbackLocale: 'fr',
  messages: { fr, en, bm }
})
