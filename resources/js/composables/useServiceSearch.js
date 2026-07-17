import { reactive, ref } from 'vue'
import { api } from '@/lib/api'

export function useServiceSearch(initialFilters = {}) {
    const services = ref([])
    const loading = ref(false)
    const error = ref('')
    const pagination = ref({
        current_page: 1,
        last_page: 1,
        total: 0,
    })
    const filters = reactive({
        keyword: initialFilters.keyword ?? '',
        category: initialFilters.category ?? '',
        city: initialFilters.city ?? '',
        sort: initialFilters.sort ?? 'recent',
    })

    async function search(page = 1) {
        loading.value = true
        error.value = ''

        try {
            const { data } = await api.get('/services-search', {
                params: {
                    ...filters,
                    page,
                },
            })
            services.value = data.data ?? []
            pagination.value = {
                current_page: data.current_page ?? 1,
                last_page: data.last_page ?? 1,
                total: data.total ?? services.value.length,
            }
        } catch (requestError) {
            services.value = []
            error.value = requestError.response?.data?.message || true
        } finally {
            loading.value = false
        }
    }

    function reset() {
        filters.keyword = ''
        filters.category = ''
        filters.city = ''
        filters.sort = 'recent'
        return search()
    }

    return {
        services,
        loading,
        error,
        pagination,
        filters,
        search,
        reset,
    }
}
