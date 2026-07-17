import { useI18n } from 'vue-i18n'

export function useServiceCategoryLabel() {
    const { t, te } = useI18n()

    const categoryLabel = name => {
        if (!name) return ''
        const key = `serviceCategories.${name.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase()}`
        return te(key) ? t(key) : name
    }

    return { categoryLabel }
}
