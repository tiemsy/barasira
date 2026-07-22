<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
    recentMissions: { type: Array, default: () => [] },
})

const page = usePage()
const { locale, t } = useI18n()
const user = computed(() => page.props?.auth?.user ?? null)
const statCards = computed(() => [
    { label: t('dashboard.client.total'), value: props.stats.total ?? 0, icon: 'list', tone: 'amber' },
    { label: t('dashboard.client.active'), value: props.stats.active ?? 0, icon: 'active', tone: 'blue' },
    { label: t('dashboard.client.completed'), value: props.stats.completed ?? 0, icon: 'completed', tone: 'green' },
    { label: t('dashboard.common.unread'), value: props.stats.unread_messages ?? 0, icon: 'mail', tone: 'purple' },
])
const statusLabel = status => t(`missions.status.${status}`)
const formatDate = value => value
    ? new Intl.DateTimeFormat(locale.value, { day: '2-digit', month: 'short', year: 'numeric' }).format(new Date(value))
    : t('missions.index.notDefined')
</script>

<template>
    <Head :title="$t('dashboard.client_title')" />
    <AppLayout>
        <main class="user-dashboard">
            <section class="user-dashboard__hero">
                <div class="user-dashboard__container user-dashboard__hero-grid">
                    <div>
                        <span class="user-dashboard__eyebrow">{{ $t('dashboard.client.eyebrow') }}</span>
                        <h1>{{ $t('dashboard.common.welcome', { name: user?.first_name || user?.name }) }}</h1>
                        <p>{{ $t('dashboard.client.subtitle') }}</p>
                    </div>
                    <Link href="/missions/create" class="dashboard-primary-action">
                        <DashboardIcon name="plus" />{{ $t('missions.create') }}
                    </Link>
                </div>
            </section>

            <section class="user-dashboard__content">
                <div class="user-dashboard__container">
                    <div class="dashboard-stats">
                        <article v-for="card in statCards" :key="card.label" class="dashboard-stat">
                            <span :class="`dashboard-stat__icon dashboard-stat__icon--${card.tone}`"><DashboardIcon :name="card.icon" /></span>
                            <span><small>{{ card.label }}</small><strong>{{ card.value }}</strong></span>
                        </article>
                    </div>

                    <div class="dashboard-layout">
                        <section class="dashboard-panel">
                            <header class="dashboard-panel__header">
                                <div><span>{{ $t('dashboard.common.recentActivity') }}</span><h2>{{ $t('dashboard.client.recentMissions') }}</h2></div>
                                <Link href="/missions/index">{{ $t('dashboard.common.viewAll') }} <DashboardIcon name="arrow" /></Link>
                            </header>
                            <div v-if="recentMissions.length" class="dashboard-list">
                                <Link v-for="mission in recentMissions" :key="mission.id" :href="`/missions/${mission.slug}`" class="dashboard-list-item">
                                    <span class="dashboard-list-item__icon"><DashboardIcon name="missions" /></span>
                                    <span class="dashboard-list-item__copy">
                                        <strong>{{ mission.title }}</strong>
                                        <small>{{ mission.service?.name || $t('missions.index.notDefined') }} · {{ formatDate(mission.created_at) }}</small>
                                    </span>
                                    <span :class="`dashboard-status dashboard-status--${mission.status}`">{{ statusLabel(mission.status) }}</span>
                                </Link>
                            </div>
                            <div v-else class="dashboard-empty">{{ $t('dashboard.client.empty') }}</div>
                        </section>

                        <aside class="dashboard-shortcuts">
                            <h2>{{ $t('dashboard.common.shortcuts') }}</h2>
                            <Link href="/missions/index"><DashboardIcon name="list" /><span><strong>{{ $t('missions.my_missions') }}</strong><small>{{ $t('dashboard.client.manageMissions') }}</small></span><DashboardIcon name="chevron" /></Link>
                            <Link href="/messages"><DashboardIcon name="messages" /><span><strong>{{ $t('navigation.messages') }}</strong><small>{{ $t('dashboard.common.openConversations') }}</small></span><DashboardIcon name="chevron" /></Link>
                            <Link href="/services"><DashboardIcon name="search" /><span><strong>{{ $t('navigation.services') }}</strong><small>{{ $t('dashboard.client.findProvider') }}</small></span><DashboardIcon name="chevron" /></Link>
                        </aside>
                    </div>
                </div>
            </section>
        </main>
    </AppLayout>
</template>

<style scoped lang="scss" src="../../scss/pages/_user-dashboard.scss"></style>
