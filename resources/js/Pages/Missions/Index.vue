<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import CancelIcon from '@/Components/Icons/CancelIcon.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import missionService from '@/composables/missionService'
import { useToastStore } from '@/stores/toast'
import { useConfirmDialog } from '@/composables/useConfirmDialog'

const props = defineProps({
    missions: {
        type: Object,
        required: true,
    },
    prestataires: {
        type: Array,
        default: () => [],
    },
})

const { locale, t } = useI18n()
const page = usePage()
const toast = useToastStore()
const { confirm } = useConfirmDialog()
const defaultFilters = () => ({
    search: '',
    statuses: [],
    date_start: '',
    date_end: '',
    prestataire_id: '',
    price_min: '',
    price_max: '',
    sort: 'date_desc',
})

const filters = ref(defaultFilters())
const missionItems = ref(props.missions.data ?? [])
const currentPage = ref(props.missions.current_page ?? 1)
const nextPageUrl = ref(props.missions.next_page_url ?? null)
const loading = ref(false)
const filtersOpen = ref(false)
const loadMoreTrigger = ref(null)
let observer = null
let filterTimer = null
let requestVersion = 0

const statuses = computed(() => [
    { value: 'pending', label: t('missions.status.pending') },
    { value: 'in_progress', label: t('missions.status.in_progress') },
    { value: 'completed', label: t('missions.status.completed') },
    { value: 'cancelled', label: t('missions.status.cancelled') },
])

const statusLabel = status => statuses.value.find(item => item.value === status)?.label ?? status
const hasActiveFilters = computed(() => Object.entries(filters.value).some(([key, value]) => {
    if (key === 'sort') return value !== 'date_desc'
    return Array.isArray(value) ? value.length > 0 : Boolean(value)
}))
const visibleStats = computed(() => ({
    total: missionItems.value.length,
    active: missionItems.value.filter(item => ['pending', 'in_progress'].includes(item.status)).length,
    completed: missionItems.value.filter(item => item.status === 'completed').length,
}))

function toggleStatus(status) {
    filters.value.statuses = filters.value.statuses.includes(status)
        ? filters.value.statuses.filter(item => item !== status)
        : [...filters.value.statuses, status]
}

function resetFilters() {
    filters.value = defaultFilters()
}

async function loadMissions({ reset = false } = {}) {
    if (loading.value && !reset) return

    const version = ++requestVersion
    loading.value = true

    try {
        const page = reset ? 1 : currentPage.value + 1
        const { data } = await missionService.get({ page, ...filters.value })

        if (version !== requestVersion) return

        missionItems.value = reset ? data.data : [...missionItems.value, ...data.data]
        currentPage.value = data.current_page
        nextPageUrl.value = data.next_page_url
    } catch {
        if (version === requestVersion) {
            toast.show(t('missions.index.loadError'), 'error')
        }
    } finally {
        if (version === requestVersion) loading.value = false
    }
}

async function cancelMission(mission) {
    if (!await confirm({ title: t('confirmDialog.cancelActionTitle'), message: t('missions.index.cancelConfirm'), confirmLabel: t('confirmDialog.confirmAction'), tone: 'warning' })) return

    try {
        const { data } = await missionService.update(mission.id, { status: 'cancelled' })
        Object.assign(mission, data.data)
        toast.show(t('missions.messages.cancelled_success'))
    } catch {
        toast.show(t('missions.index.cancelError'), 'error')
    }
}

async function deleteMission(mission) {
    if (!await confirm({ title: t('confirmDialog.deleteTitle'), message: t('missions.index.deleteConfirm'), confirmLabel: t('confirmDialog.delete') })) return

    try {
        await missionService.remove(mission.id)
        missionItems.value = missionItems.value.filter(item => item.id !== mission.id)
        toast.show(t('missions.index.deleted'))
    } catch {
        toast.show(t('missions.index.deleteError'), 'error')
    }
}

function formatDate(value) {
    if (!value) return t('missions.index.notDefined')

    return new Intl.DateTimeFormat(locale.value, {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(new Date(value))
}

function formatPrice(value) {
    if (value === null || value === undefined || value === '') return t('missions.index.priceOnRequest')

    return new Intl.NumberFormat(locale.value, {
        style: 'currency',
        currency: 'XOF',
        maximumFractionDigits: 0,
    }).format(Number(value))
}

function providerName(provider) {
    if (!provider) return t('missions.index.unassigned')
    return provider.name ?? [provider.first_name, provider.last_name].filter(Boolean).join(' ')
}

function missionReview(mission) {
    return mission.reviews?.find(review => review.reviewer_id === page.props.auth?.user?.id) ?? null
}

function canAccessReview(mission) {
    return page.props.auth?.user?.role === 'client'
        && mission.client_id === page.props.auth.user.id
        && mission.status === 'completed'
        && Boolean(mission.prestataire_id)
}

function reviewActionLabel(mission) {
    const review = missionReview(mission)

    if (!review) return t('reviews.rateAction')
    return review.edit_count < 1 ? t('reviews.editAction') : t('reviews.viewAction')
}

watch(filters, () => {
    window.clearTimeout(filterTimer)
    filterTimer = window.setTimeout(() => loadMissions({ reset: true }), 350)
}, { deep: true })

onMounted(() => {
    observer = new IntersectionObserver(entries => {
        if (entries[0]?.isIntersecting && nextPageUrl.value) loadMissions()
    }, { rootMargin: '240px' })

    if (loadMoreTrigger.value) observer.observe(loadMoreTrigger.value)
})

onBeforeUnmount(() => {
    window.clearTimeout(filterTimer)
    observer?.disconnect()
})
</script>

<template>
    <Head :title="$t('missions.my_missions')" />

    <AppLayout>
        <main class="mission-index">
            <section class="mission-index__hero">
                <div class="mission-index__container">
                    <div class="mission-index__hero-content">
                        <div>
                            <span class="mission-index__eyebrow">{{ $t('missions.index.eyebrow') }}</span>
                            <h1>{{ $t('missions.my_missions') }}</h1>
                            <p>{{ $t('missions.index.subtitle') }}</p>
                        </div>

                        <Link href="/missions/create" class="mission-index__create">
                            <span aria-hidden="true">＋</span>
                            {{ $t('missions.create') }}
                        </Link>
                    </div>

                    <div class="mission-index__stats">
                        <article>
                            <span>{{ $t('missions.index.visible') }}</span>
                            <strong>{{ visibleStats.total }}</strong>
                        </article>
                        <article>
                            <span>{{ $t('missions.index.active') }}</span>
                            <strong>{{ visibleStats.active }}</strong>
                        </article>
                        <article>
                            <span>{{ $t('missions.status.completed') }}</span>
                            <strong>{{ visibleStats.completed }}</strong>
                        </article>
                    </div>
                </div>
            </section>

            <section class="mission-index__body">
                <div class="mission-index__container">
                    <div class="mission-index__toolbar">
                        <label class="mission-search">
                            <DashboardIcon name="search" />
                            <span class="sr-only">{{ $t('missions.index.search') }}</span>
                            <input v-model="filters.search" type="search" :placeholder="$t('missions.index.searchPlaceholder')">
                        </label>

                        <button
                            type="button"
                            class="mission-filter-toggle"
                            :class="{ active: hasActiveFilters }"
                            :aria-expanded="filtersOpen"
                            @click="filtersOpen = !filtersOpen"
                        >
                            <DashboardIcon name="filters" />
                            {{ $t('missions.index.filters') }}
                            <span v-if="hasActiveFilters" class="mission-filter-toggle__dot"></span>
                        </button>

                        <select v-model="filters.sort" class="mission-sort" :aria-label="$t('missions.index.sort')">
                            <option value="date_desc">{{ $t('missions.index.sortNewest') }}</option>
                            <option value="date_asc">{{ $t('missions.index.sortOldest') }}</option>
                            <option value="price_asc">{{ $t('missions.index.sortPriceAsc') }}</option>
                            <option value="price_desc">{{ $t('missions.index.sortPriceDesc') }}</option>
                        </select>
                    </div>

                    <div class="mission-status-tabs" :aria-label="$t('missions.index.statusFilters')">
                        <button
                            v-for="status in statuses"
                            :key="status.value"
                            type="button"
                            :class="{ active: filters.statuses.includes(status.value) }"
                            @click="toggleStatus(status.value)"
                        >
                            <span :class="`mission-status-dot mission-status-dot--${status.value}`"></span>
                            {{ status.label }}
                        </button>
                    </div>

                    <div v-show="filtersOpen" class="mission-filters">
                        <label>
                            <span>{{ $t('missions.index.startDate') }}</span>
                            <input v-model="filters.date_start" type="date">
                        </label>
                        <label>
                            <span>{{ $t('missions.index.endDate') }}</span>
                            <input v-model="filters.date_end" type="date">
                        </label>
                        <label>
                            <span>{{ $t('missions.fields.provider') }}</span>
                            <select v-model="filters.prestataire_id">
                                <option value="">{{ $t('missions.index.allProviders') }}</option>
                                <option v-for="provider in prestataires" :key="provider.id" :value="provider.id">
                                    {{ providerName(provider) }}
                                </option>
                            </select>
                        </label>
                        <label>
                            <span>{{ $t('missions.index.minimumPrice') }}</span>
                            <input v-model="filters.price_min" type="number" min="0" placeholder="0">
                        </label>
                        <label>
                            <span>{{ $t('missions.index.maximumPrice') }}</span>
                            <input v-model="filters.price_max" type="number" min="0" placeholder="0">
                        </label>
                        <button v-if="hasActiveFilters" type="button" class="mission-filters__reset" @click="resetFilters">
                            {{ $t('missions.index.reset') }}
                        </button>
                    </div>

                    <div v-if="loading && missionItems.length === 0" class="mission-grid" aria-busy="true">
                        <div v-for="item in 6" :key="item" class="mission-card-skeleton"></div>
                    </div>

                    <div v-else-if="missionItems.length" class="mission-grid">
                        <article v-for="mission in missionItems" :key="mission.id" class="mission-list-card">
                            <div class="mission-list-card__top">
                                <span :class="`mission-status mission-status--${mission.status}`">
                                    {{ statusLabel(mission.status) }}
                                </span>
                                <span class="mission-list-card__id">#{{ mission.id }}</span>
                            </div>

                            <div class="mission-list-card__content">
                                <h2>{{ mission.title }}</h2>
                                <p>{{ mission.description }}</p>
                            </div>

                            <div class="mission-list-card__meta">
                                <span><DashboardIcon name="location" />{{ mission.city || mission.address || $t('missions.index.notDefined') }}</span>
                                <span><DashboardIcon name="wallet" />{{ formatPrice(mission.price) }}</span>
                            </div>

                            <div class="mission-list-card__timeline">
                                <div>
                                    <span>{{ $t('missions.index.startDate') }}</span>
                                    <strong>{{ formatDate(mission.date_start) }}</strong>
                                </div>
                                <DashboardIcon name="arrow" />
                                <div>
                                    <span>{{ $t('missions.index.endDate') }}</span>
                                    <strong>{{ formatDate(mission.date_end) }}</strong>
                                </div>
                            </div>

                            <div class="mission-list-card__footer">
                                <div class="mission-provider">
                                    <span class="mission-provider__avatar" aria-hidden="true">
                                        {{ providerName(mission.prestataire).charAt(0).toUpperCase() }}
                                    </span>
                                    <span>
                                        <small>{{ $t('missions.fields.provider') }}</small>
                                        <strong>{{ providerName(mission.prestataire) }}</strong>
                                    </span>
                                </div>

                                <div class="mission-list-card__actions">
                                    <Link
                                        v-if="canAccessReview(mission)"
                                        :href="`/missions/${mission.slug}#review`"
                                        class="mission-action mission-action--review"
                                    >
                                        <DashboardIcon name="rating" />
                                        {{ reviewActionLabel(mission) }}
                                    </Link>
                                    <Link :href="`/missions/${mission.slug}`" class="mission-action mission-action--primary">
                                        {{ $t('missions.actions.view') }}
                                    </Link>
                                    <Link v-if="mission.status === 'pending'" :href="`/missions/${mission.id}/edit`" class="mission-action mission-action--edit" :aria-label="$t('missions.edit')">
                                        <DashboardIcon name="edit" />
                                    </Link>
                                    <button v-if="['pending', 'in_progress'].includes(mission.status)" type="button" class="mission-action" :aria-label="$t('missions.actions.cancel')" @click="cancelMission(mission)">
                                        <CancelIcon />
                                    </button>
                                    <button v-if="mission.status === 'pending'" type="button" class="mission-action mission-action--danger" :aria-label="$t('missions.index.delete')" @click="deleteMission(mission)">
                                        <DashboardIcon name="delete" />
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div v-else class="mission-empty">
                        <span class="mission-empty__icon"><DashboardIcon name="list" /></span>
                        <h2>{{ hasActiveFilters ? $t('missions.index.noResults') : $t('missions.no_missions') }}</h2>
                        <p>{{ hasActiveFilters ? $t('missions.index.noResultsDescription') : $t('missions.index.emptyDescription') }}</p>
                        <button v-if="hasActiveFilters" type="button" class="mission-index__create" @click="resetFilters">
                            {{ $t('missions.index.reset') }}
                        </button>
                        <Link v-else href="/missions/create" class="mission-index__create">{{ $t('missions.create') }}</Link>
                    </div>

                    <div ref="loadMoreTrigger" class="mission-loader" aria-live="polite">
                        <span v-if="loading"><DashboardIcon name="loading" />{{ $t('missions.index.loading') }}</span>
                        <span v-else-if="missionItems.length && !nextPageUrl">{{ $t('missions.index.end') }}</span>
                    </div>
                </div>
            </section>
        </main>
    </AppLayout>
</template>

<style scoped lang="scss" src="../../../scss/pages/missions/_index.scss"></style>
