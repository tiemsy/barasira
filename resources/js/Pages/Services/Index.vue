<script setup>
import { computed, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ServiceCard from '@/Components/ServiceCard.vue'
import { useServiceSearch } from '@/composables/useServiceSearch'
import { useI18n } from 'vue-i18n'
import { useServiceCategoryLabel } from '@/composables/useServiceCategoryLabel'

const props = defineProps({
    categories: { type: Array, default: () => [] },
    cities: { type: Array, default: () => [] },
})

const page = usePage()
const { t } = useI18n()
const { categoryLabel } = useServiceCategoryLabel()
const query = new URLSearchParams((page.url ?? '').split('?')[1] ?? '')
const {
    services,
    loading,
    error,
    pagination,
    filters,
    search,
    reset,
} = useServiceSearch({
    keyword: query.get('keyword') ?? '',
    category: query.get('category') ?? '',
    city: query.get('city') ?? '',
    sort: query.get('sort') ?? 'recent',
})

const hasFilters = computed(() =>
    Boolean(filters.keyword || filters.category || filters.city || filters.sort !== 'recent')
)
const resultLabel = computed(() => {
    return t('serviceIndex.results', { count: pagination.value.total })
})

let debounceTimer
function updateSearch({ debounce = false } = {}) {
    window.clearTimeout(debounceTimer)

    const run = () => {
        syncUrl()
        search(1)
    }

    if (debounce) {
        debounceTimer = window.setTimeout(run, 350)
        return
    }

    run()
}

function syncUrl() {
    const data = Object.fromEntries(
        Object.entries(filters).filter(([, value]) => value && value !== 'recent')
    )
    const queryString = new URLSearchParams(data).toString()
    window.history.replaceState({}, '', queryString ? `/services?${queryString}` : '/services')
}

async function resetFilters() {
    window.history.replaceState({}, '', '/services')
    await reset()
}

onMounted(() => search())
</script>

<template>
    <AppLayout :title="$t('serviceIndex.title')">
        <main class="services-page">
            <section class="services-hero">
                <div class="services-container services-hero__content">
                    <span class="services-kicker">{{ $t('serviceIndex.kicker') }}</span>
                    <h1>{{ $t('serviceIndex.title') }}</h1>
                    <p>{{ $t('serviceIndex.subtitle') }}</p>

                    <form class="services-search" @submit.prevent="updateSearch()">
                        <span aria-hidden="true">⌕</span>
                        <input
                            v-model.trim="filters.keyword"
                            type="search"
                            :placeholder="$t('serviceIndex.searchPlaceholder')"
                            :aria-label="$t('serviceIndex.searchLabel')"
                            @input="updateSearch({ debounce: true })"
                        >
                        <button type="submit">{{ $t('serviceIndex.search') }}</button>
                    </form>
                </div>
            </section>

            <section class="services-catalog">
                <div class="services-container">
                    <div class="services-filter-panel">
                        <div class="services-filter-panel__heading">
                            <div>
                                <span>{{ $t('serviceIndex.refine') }}</span>
                                <strong>{{ resultLabel }}</strong>
                            </div>
                            <button v-if="hasFilters" type="button" @click="resetFilters">
                                {{ $t('serviceIndex.reset') }}
                            </button>
                        </div>

                        <div class="services-filter-grid">
                            <label>
                                <span>{{ $t('serviceIndex.category') }}</span>
                                <select v-model="filters.category" @change="updateSearch()">
                                    <option value="">{{ $t('serviceIndex.allCategories') }}</option>
                                    <option v-for="category in props.categories" :key="category.id" :value="String(category.id)">
                                        {{ categoryLabel(category.name) }}
                                    </option>
                                </select>
                            </label>
                            <label>
                                <span>{{ $t('serviceIndex.city') }}</span>
                                <select v-model="filters.city" @change="updateSearch()">
                                    <option value="">{{ $t('serviceIndex.allCities') }}</option>
                                    <option v-for="city in props.cities" :key="city.id" :value="String(city.id)">
                                        {{ city.name }}
                                    </option>
                                </select>
                            </label>
                            <label>
                                <span>{{ $t('serviceIndex.sort') }}</span>
                                <select v-model="filters.sort" @change="updateSearch()">
                                    <option value="recent">{{ $t('serviceIndex.newest') }}</option>
                                    <option value="price_asc">{{ $t('serviceIndex.lowestPrice') }}</option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <div v-if="loading" class="services-grid">
                        <div v-for="item in 6" :key="item" class="service-card service-card--skeleton" />
                    </div>

                    <div v-else-if="error" class="services-empty">
                        <span>!</span>
                        <h2>{{ $t('serviceIndex.unavailableTitle') }}</h2>
                        <p>{{ typeof error === 'string' ? error : $t('serviceIndex.unavailableText') }}</p>
                        <button type="button" @click="search()">{{ $t('serviceIndex.retry') }}</button>
                    </div>

                    <div v-else-if="!services.length" class="services-empty">
                        <span>⌕</span>
                        <h2>{{ $t('serviceIndex.emptyTitle') }}</h2>
                        <p>{{ $t('serviceIndex.emptyText') }}</p>
                        <button type="button" @click="resetFilters">{{ $t('serviceIndex.viewAll') }}</button>
                    </div>

                    <section v-else class="services-grid" aria-live="polite">
                        <ServiceCard v-for="service in services" :key="service.id" :service="service" />
                    </section>

                    <nav v-if="pagination.last_page > 1" class="services-pagination" :aria-label="$t('serviceIndex.pagination')">
                        <button
                            type="button"
                            :disabled="pagination.current_page === 1 || loading"
                            @click="search(pagination.current_page - 1)"
                        >
                            ← {{ $t('serviceIndex.previous') }}
                        </button>
                        <span>{{ $t('serviceIndex.page', { current: pagination.current_page, total: pagination.last_page }) }}</span>
                        <button
                            type="button"
                            :disabled="pagination.current_page === pagination.last_page || loading"
                            @click="search(pagination.current_page + 1)"
                        >
                            {{ $t('serviceIndex.next') }} →
                        </button>
                    </nav>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
