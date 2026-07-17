<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import StatsCard from '@/Components/Admin/StatsCard.vue'
import RecentUsers from '@/Components/Admin/RecentUsers.vue'
import RecentServices from '@/Components/Admin/RecentServices.vue'
import UsersChart from '@/Components/Admin/Charts/UsersChart.vue'
import ServicesChart from '@/Components/Admin/Charts/ServicesChart.vue'

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
    recentUsers: { type: Array, default: () => [] },
    recentServices: { type: Array, default: () => [] },
    userStats: { type: Object, default: () => ({}) },
    missionStats: { type: Object, default: () => ({}) },
    serviceStats: { type: Array, default: () => [] },
})

const page = usePage()
const { t } = useI18n()
const user = computed(() => page.props?.auth?.user ?? null)
const statCards = computed(() => [
    { key: 'users', label: t('adminDashboard.stats.users'), value: props.stats.users ?? 0, icon: 'fa-users', tone: 'green' },
    { key: 'providers', label: t('adminDashboard.stats.providers'), value: props.stats.providers ?? 0, icon: 'fa-user-gear', tone: 'blue' },
    { key: 'clients', label: t('adminDashboard.stats.clients'), value: props.stats.clients ?? 0, icon: 'fa-user-group', tone: 'purple' },
    { key: 'services', label: t('adminDashboard.stats.services'), value: props.stats.services ?? 0, icon: 'fa-layer-group', tone: 'amber' },
    { key: 'missions', label: t('adminDashboard.stats.missions'), value: props.stats.missions ?? 0, icon: 'fa-briefcase', tone: 'rose' },
    { key: 'active', label: t('adminDashboard.stats.activeMissions'), value: props.stats.active_missions ?? 0, icon: 'fa-bolt', tone: 'teal' },
])
const missionStatuses = computed(() => ['pending', 'in_progress', 'completed', 'cancelled'].map(status => ({
    status,
    value: props.missionStats[status] ?? 0,
})))
</script>

<template>
    <Head :title="$t('adminDashboard.metaTitle')" />
    <AppLayout>
        <main class="admin-dashboard">
            <section class="admin-dashboard__hero">
                <div class="admin-dashboard__container admin-dashboard__hero-layout">
                    <div>
                        <span class="admin-dashboard__eyebrow"><i class="fas fa-shield-halved"></i>{{ $t('adminDashboard.eyebrow') }}</span>
                        <h1>{{ $t('adminDashboard.welcome', { name: user?.firstname || user?.name || '' }) }}</h1>
                        <p>{{ $t('adminDashboard.subtitle') }}</p>
                    </div>
                    <div class="admin-dashboard__hero-actions">
                        <Link href="/admin/users" class="admin-action admin-action--secondary"><i class="fas fa-users-gear"></i>{{ $t('adminDashboard.actions.users') }}</Link>
                        <Link href="/admin/services" class="admin-action admin-action--secondary"><i class="fas fa-layer-group"></i>{{ $t('adminDashboard.actions.services') }}</Link>
                        <Link href="/missions/index" class="admin-action admin-action--primary"><i class="fas fa-briefcase"></i>{{ $t('adminDashboard.actions.missions') }}</Link>
                    </div>
                </div>
            </section>

            <section class="admin-dashboard__body">
                <div class="admin-dashboard__container">
                    <section class="admin-stat-grid" :aria-label="$t('adminDashboard.overview')">
                        <StatsCard v-for="card in statCards" :key="card.key" v-bind="card" />
                    </section>

                    <section class="admin-dashboard__charts">
                        <UsersChart :data="userStats" />
                        <ServicesChart :items="serviceStats" />
                    </section>

                    <section class="admin-mission-overview">
                        <header>
                            <div><span>{{ $t('adminDashboard.activity') }}</span><h2>{{ $t('adminDashboard.missionDistribution') }}</h2></div>
                            <strong>{{ stats.missions ?? 0 }}</strong>
                        </header>
                        <div class="admin-mission-statuses">
                            <article v-for="item in missionStatuses" :key="item.status">
                                <span :class="`admin-status-dot admin-status-dot--${item.status}`"></span>
                                <small>{{ $t(`missions.status.${item.status}`) }}</small>
                                <strong>{{ item.value }}</strong>
                            </article>
                        </div>
                    </section>

                    <section class="admin-dashboard__recent">
                        <RecentServices :services="recentServices" />
                        <RecentUsers :users="recentUsers" />
                    </section>
                </div>
            </section>
        </main>
    </AppLayout>
</template>

<style lang="scss" src="../../../scss/pages/admin/_dashboard.scss"></style>
