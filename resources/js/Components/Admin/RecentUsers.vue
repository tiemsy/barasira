<script setup>
import { useI18n } from 'vue-i18n'
import DashboardIcon from '@/Components/DashboardIcon.vue'

defineProps({ users: { type: Array, default: () => [] } })
const { t } = useI18n()
const fullName = user => `${user.first_name ?? ''} ${user.last_name ?? ''}`.trim()
const roleLabel = role => t(`navbar.roles.${role === 'prestataire' ? 'provider' : role}`)
</script>

<template>
    <article class="admin-panel admin-recent-panel">
        <header class="admin-panel__header">
            <div><span>{{ $t('adminDashboard.latest') }}</span><h2>{{ $t('adminDashboard.recentUsers') }}</h2></div>
            <DashboardIcon name="users" />
        </header>
        <ul v-if="users.length" class="admin-recent-list">
            <li v-for="user in users" :key="user.id">
                <span class="admin-user-avatar">
                    <img v-if="user.avatar_url" :src="user.avatar_url" alt="">
                    <b v-else>{{ fullName(user).charAt(0) || '?' }}</b>
                </span>
                <span class="admin-recent-copy"><strong>{{ fullName(user) }}</strong><small>{{ user.email }}</small></span>
                <span class="admin-role-badge">{{ roleLabel(user.role) }}</span>
            </li>
        </ul>
        <p v-else class="admin-empty">{{ $t('adminDashboard.noRecentUsers') }}</p>
    </article>
</template>
