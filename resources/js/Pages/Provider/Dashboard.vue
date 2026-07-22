<script setup>
import { computed, ref } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
    assignedMissions: { type: Array, default: () => [] },
    availableMissions: { type: Array, default: () => [] },
    missionInvitations: { type: Array, default: () => [] },
})
const page = usePage()
const { locale, t } = useI18n()
const user = computed(() => page.props?.auth?.user ?? null)
const statCards = computed(() => [
    { label: t('dashboard.provider.services'), value: props.stats.services ?? 0, icon: 'tools', tone: 'amber' },
    { label: t('dashboard.provider.active'), value: props.stats.active_missions ?? 0, icon: 'active', tone: 'blue' },
    { label: t('dashboard.provider.applications'), value: props.stats.applications ?? 0, icon: 'applications', tone: 'purple' },
    { label: t('dashboard.provider.rating'), value: `${props.stats.rating ?? 0}/5`, icon: 'rating', tone: 'green' },
])
const formatPrice = value => value ? new Intl.NumberFormat(locale.value).format(value) + ' FCFA' : t('missions.index.priceOnRequest')
const acceptingInvitation = ref(null)
const invitationError = ref('')

function acceptInvitation(invitation) {
    acceptingInvitation.value = invitation.id
    invitationError.value = ''
    router.post(`/mission-invitations/${invitation.id}/accept`, {}, {
        preserveScroll: true,
        onError: errors => { invitationError.value = errors.invitation || errors.mission || t('missions.invitation.acceptError') },
        onFinish: () => { acceptingInvitation.value = null },
    })
}
</script>

<template>
    <Head :title="$t('dashboard.provider_title')" />
    <AppLayout>
        <main class="user-dashboard">
            <section class="user-dashboard__hero">
                <div class="user-dashboard__container user-dashboard__hero-grid">
                    <div>
                        <span class="user-dashboard__eyebrow">{{ $t('dashboard.provider.eyebrow') }}</span>
                        <h1>{{ $t('dashboard.common.welcome', { name: user?.first_name || user?.name }) }}</h1>
                        <p>{{ $t('dashboard.provider.subtitle') }}</p>
                    </div>
                    <Link href="/services" class="dashboard-primary-action"><DashboardIcon name="store" />{{ $t('dashboard.provider.manageServices') }}</Link>
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
                        <div class="dashboard-stack">
                            <section class="dashboard-panel dashboard-panel--invitations">
                                <header class="dashboard-panel__header"><div><span>{{ $t('dashboard.provider.invitationsEyebrow') }}</span><h2>{{ $t('dashboard.provider.invitationsTitle') }}</h2></div></header>
                                <div v-if="missionInvitations.length" class="dashboard-invitations">
                                    <article v-for="invitation in missionInvitations" :key="invitation.id" class="dashboard-invitation">
                                        <Link :href="`/missions/${invitation.mission.slug}`">
                                            <strong>{{ invitation.mission.title }}</strong>
                                            <small>{{ invitation.client.first_name }} {{ invitation.client.last_name }} · {{ invitation.mission.service?.name }}</small>
                                            <b>{{ formatPrice(invitation.mission.price) }}</b>
                                        </Link>
                                        <button type="button" :disabled="acceptingInvitation === invitation.id" @click="acceptInvitation(invitation)">
                                            {{ acceptingInvitation === invitation.id ? $t('missions.invitation.accepting') : $t('missions.invitation.accept') }}
                                        </button>
                                    </article>
                                </div>
                                <div v-else class="dashboard-empty">{{ $t('dashboard.provider.noInvitations') }}</div>
                                <p v-if="invitationError" class="dashboard-invitation-error" role="alert">{{ invitationError }}</p>
                            </section>
                            <section class="dashboard-panel">
                                <header class="dashboard-panel__header"><div><span>{{ $t('dashboard.provider.work') }}</span><h2>{{ $t('dashboard.provider.assignedMissions') }}</h2></div></header>
                                <div v-if="assignedMissions.length" class="dashboard-list">
                                    <Link v-for="mission in assignedMissions" :key="mission.id" :href="`/missions/${mission.slug}`" class="dashboard-list-item">
                                        <span class="dashboard-list-item__icon"><DashboardIcon name="missions" /></span>
                                        <span class="dashboard-list-item__copy"><strong>{{ mission.title }}</strong><small>{{ mission.client?.first_name }} {{ mission.client?.last_name }}</small></span>
                                        <span :class="`dashboard-status dashboard-status--${mission.status}`">{{ $t(`missions.status.${mission.status}`) }}</span>
                                    </Link>
                                </div>
                                <div v-else class="dashboard-empty">{{ $t('dashboard.provider.noAssigned') }}</div>
                            </section>
                            <section class="dashboard-panel">
                                <header class="dashboard-panel__header"><div><span>{{ $t('dashboard.provider.opportunities') }}</span><h2>{{ $t('missions.available_missions') }}</h2></div></header>
                                <div v-if="availableMissions.length" class="dashboard-opportunities">
                                    <Link v-for="mission in availableMissions" :key="mission.id" :href="`/missions/${mission.slug}`">
                                        <span><strong>{{ mission.title }}</strong><small>{{ mission.service?.name }} · {{ mission.city || mission.address }}</small></span>
                                        <b>{{ formatPrice(mission.price) }}</b>
                                    </Link>
                                </div>
                                <div v-else class="dashboard-empty">{{ $t('dashboard.provider.noAvailable') }}</div>
                            </section>
                        </div>
                        <aside class="dashboard-shortcuts">
                            <h2>{{ $t('dashboard.common.shortcuts') }}</h2>
                            <Link href="/services"><DashboardIcon name="tools" /><span><strong>{{ $t('navigation.services') }}</strong><small>{{ $t('dashboard.provider.manageServices') }}</small></span><DashboardIcon name="chevron" /></Link>
                            <Link href="/messages"><DashboardIcon name="messages" /><span><strong>{{ $t('navigation.messages') }}</strong><small>{{ $t('dashboard.common.openConversations') }}</small></span><DashboardIcon name="chevron" /></Link>
                            <Link href="/profile"><DashboardIcon name="profile" /><span><strong>{{ $t('navigation.profile') }}</strong><small>{{ $t('dashboard.provider.improveProfile') }}</small></span><DashboardIcon name="chevron" /></Link>
                            <div class="dashboard-unread"><DashboardIcon name="mail" /><span>{{ $t('dashboard.common.unread') }}</span><strong>{{ stats.unread_messages ?? 0 }}</strong></div>
                        </aside>
                    </div>
                </div>
            </section>
        </main>
    </AppLayout>
</template>

<style scoped lang="scss" src="../../../scss/pages/_user-dashboard.scss"></style>
