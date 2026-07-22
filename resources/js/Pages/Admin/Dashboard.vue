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
import DashboardIcon from '@/Components/DashboardIcon.vue'

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
    recentUsers: { type: Array, default: () => [] },
    recentServices: { type: Array, default: () => [] },
    userStats: { type: Object, default: () => ({}) },
    missionStats: { type: Object, default: () => ({}) },
    serviceStats: { type: Array, default: () => [] },
    transactionStats: { type: Object, default: () => ({}) },
    paymentStats: { type: Object, default: () => ({}) },
    paymentMethodStats: { type: Array, default: () => [] },
    recentTransactions: { type: Array, default: () => [] },
})

const page = usePage()
const { t, locale } = useI18n()
const user = computed(() => page.props?.auth?.user ?? null)
const statCards = computed(() => [
    { key: 'users', label: t('adminDashboard.stats.users'), value: props.stats.users ?? 0, icon: 'users', tone: 'green' },
    { key: 'providers', label: t('adminDashboard.stats.providers'), value: props.stats.providers ?? 0, icon: 'provider', tone: 'blue' },
    { key: 'clients', label: t('adminDashboard.stats.clients'), value: props.stats.clients ?? 0, icon: 'users', tone: 'purple' },
    { key: 'services', label: t('adminDashboard.stats.services'), value: props.stats.services ?? 0, icon: 'services', tone: 'amber' },
    { key: 'missions', label: t('adminDashboard.stats.missions'), value: props.stats.missions ?? 0, icon: 'missions', tone: 'rose' },
    { key: 'active', label: t('adminDashboard.stats.activeMissions'), value: props.stats.active_missions ?? 0, icon: 'active', tone: 'teal' },
])
const missionStatuses = computed(() => ['pending', 'in_progress', 'completed', 'cancelled'].map(status => ({
    status,
    value: props.missionStats[status] ?? 0,
})))
const paymentStatuses = computed(() => ['effectue', 'en_attente', 'echoue', 'annule', 'rembourse'].map(status => ({
    status,
    count: props.paymentStats[status]?.count ?? 0,
    amount: props.paymentStats[status]?.amount ?? 0,
})))
const transactionCards = computed(() => [
    { key: 'gross', label: t('adminDashboard.transactions.grossVolume'), value: formatMoney(props.transactionStats.gross_volume) },
    { key: 'commission', label: t('adminDashboard.transactions.platformRevenue'), value: formatMoney(props.transactionStats.platform_revenue) },
    { key: 'payouts', label: t('adminDashboard.transactions.providerPayouts'), value: formatMoney(props.transactionStats.provider_payouts) },
    { key: 'total', label: t('adminDashboard.transactions.total'), value: props.transactionStats.total ?? 0 },
    { key: 'average', label: t('adminDashboard.transactions.average'), value: formatMoney(props.transactionStats.average_amount) },
    { key: 'rate', label: t('adminDashboard.transactions.successRate'), value: `${props.transactionStats.success_rate ?? 0} %` },
])

function formatMoney(value) {
    return new Intl.NumberFormat(locale.value, { style: 'currency', currency: 'XOF', maximumFractionDigits: 0 }).format(Number(value ?? 0))
}

function formatDate(value) {
    return new Intl.DateTimeFormat(locale.value, { dateStyle: 'short', timeStyle: 'short' }).format(new Date(value))
}

function personName(person) {
    return person ? [person.first_name, person.last_name].filter(Boolean).join(' ') : '—'
}

function paymentMethodLabel(method) {
    const supportedMethods = ['orange_money', 'moov_money', 'carte', 'paypal', 'unknown']
    const key = supportedMethods.includes(method) ? method : 'unknown'

    return t(`adminDashboard.transactions.methods.${key}`)
}
</script>

<template>
    <Head :title="$t('adminDashboard.metaTitle')" />
    <AppLayout>
        <main class="admin-dashboard">
            <section class="admin-dashboard__hero">
                <div class="admin-dashboard__container admin-dashboard__hero-layout">
                    <div>
                        <span class="admin-dashboard__eyebrow"><DashboardIcon name="shield" />{{ $t('adminDashboard.eyebrow') }}</span>
                        <h1>{{ $t('adminDashboard.welcome', { name: user?.first_name || user?.name || '' }) }}</h1>
                        <p>{{ $t('adminDashboard.subtitle') }}</p>
                    </div>
                    <div class="admin-dashboard__hero-actions">
                        <Link href="/admin/documents?status=en_attente" class="admin-action admin-action--documents"><DashboardIcon name="certificate" /><span>{{ $t('adminDashboard.actions.documents') }}</span><b v-if="stats.pending_documents">{{ stats.pending_documents }}</b></Link>
                        <Link href="/admin/users" class="admin-action admin-action--secondary"><DashboardIcon name="users" />{{ $t('adminDashboard.actions.users') }}</Link>
                        <Link href="/admin/services" class="admin-action admin-action--secondary"><DashboardIcon name="services" />{{ $t('adminDashboard.actions.services') }}</Link>
                        <Link href="/missions/index" class="admin-action admin-action--primary"><DashboardIcon name="missions" />{{ $t('adminDashboard.actions.missions') }}</Link>
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

                    <section class="admin-transaction-overview">
                        <header class="admin-panel__header">
                            <div><span>{{ $t('adminDashboard.transactions.eyebrow') }}</span><h2>{{ $t('adminDashboard.transactions.title') }}</h2></div>
                        </header>

                        <div class="admin-transaction-cards">
                            <article v-for="card in transactionCards" :key="card.key">
                                <small>{{ card.label }}</small><strong>{{ card.value }}</strong>
                            </article>
                        </div>

                        <div class="admin-transaction-breakdowns">
                            <div>
                                <h3>{{ $t('adminDashboard.transactions.byStatus') }}</h3>
                                <ul>
                                    <li v-for="item in paymentStatuses" :key="item.status">
                                        <span>{{ $t(`adminDashboard.transactions.statuses.${item.status}`) }}</span>
                                        <strong>{{ item.count }}</strong><small>{{ formatMoney(item.amount) }}</small>
                                    </li>
                                </ul>
                            </div>
                            <div>
                                <h3>{{ $t('adminDashboard.transactions.byMethod') }}</h3>
                                <ul>
                                    <li v-for="item in paymentMethodStats" :key="item.method">
                                        <span>{{ paymentMethodLabel(item.method) }}</span><strong>{{ item.count }}</strong><small>{{ formatMoney(item.amount) }}</small>
                                    </li>
                                    <li v-if="!paymentMethodStats.length" class="admin-empty">{{ $t('adminDashboard.transactions.empty') }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="admin-transaction-table-wrap">
                            <h3>{{ $t('adminDashboard.transactions.recent') }}</h3>
                            <table class="admin-transaction-table">
                                <thead><tr><th>{{ $t('adminDashboard.transactions.reference') }}</th><th>{{ $t('adminDashboard.transactions.mission') }}</th><th>{{ $t('adminDashboard.transactions.payer') }}</th><th>{{ $t('adminDashboard.transactions.receiver') }}</th><th>{{ $t('adminDashboard.transactions.method') }}</th><th>{{ $t('adminDashboard.transactions.status') }}</th><th>{{ $t('adminDashboard.transactions.amount') }}</th><th>{{ $t('adminDashboard.transactions.commission') }}</th><th>{{ $t('adminDashboard.transactions.providerNet') }}</th><th>{{ $t('adminDashboard.transactions.date') }}</th></tr></thead>
                                <tbody>
                                    <tr v-for="payment in recentTransactions" :key="payment.id">
                                        <td>{{ payment.transaction_id || `#${payment.id}` }}</td><td>{{ payment.mission?.title || '—' }}</td><td>{{ personName(payment.payer) }}</td><td>{{ personName(payment.receiver) }}</td><td>{{ paymentMethodLabel(payment.method) }}</td><td><span :class="`admin-payment-status admin-payment-status--${payment.status}`">{{ $t(`adminDashboard.transactions.statuses.${payment.status}`) }}</span></td><td>{{ formatMoney(payment.amount) }}</td><td>{{ formatMoney(payment.platform_fee) }}</td><td>{{ formatMoney(payment.provider_amount) }}</td><td>{{ formatDate(payment.created_at) }}</td>
                                    </tr>
                                    <tr v-if="!recentTransactions.length"><td colspan="10" class="admin-empty">{{ $t('adminDashboard.transactions.empty') }}</td></tr>
                                </tbody>
                            </table>
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
