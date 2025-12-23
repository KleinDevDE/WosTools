import {createI18n} from 'vue-i18n'
import en from './locales/en'
import de from './locales/de'
import tr from './locales/tr'

// Get locale from localStorage (set by Laravel) or default to 'en'
const savedLocale = localStorage.getItem('locale') || 'en'

const i18n = createI18n({
    legacy: false, // Use Composition API mode
    locale: savedLocale,
    fallbackLocale: 'en',
    globalInjection: true,
    messages: {
        en,
        de,
        tr,
    },
})

export default i18n
