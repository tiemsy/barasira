import { ref } from 'vue'
import { api } from '@/lib/api'

let debounceTimer = null

const services = ref([])
const loading = ref(false)
const pagination = ref({})
const filters = ref({
    keyword: '',
    category: '',
    city: '',
})

const searchResults = (page = 1, debounce = false) => {
    if (debounce) {
        clearTimeout(debounceTimer)
        debounceTimer = setTimeout(() => runSearch(page), 400)
    } else {
        runSearch(page)
    }
}

const runSearch = async (page) => {
    loading.value = true

    try {
        const { data } = await api.get('/services-search', {
            params: { ...filters.value, page }
        })

        services.value = data.data
        pagination.value = data.meta
    } catch (error) {
        services.value = []
    } finally {
        loading.value = false
    }
}

export {
    services,
    filters,
    loading,
    pagination,
    searchResults
}
