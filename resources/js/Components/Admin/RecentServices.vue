<script setup>
defineProps({ services: { type: Array, default: () => [] } })
const providerName = service => `${service.user?.first_name ?? ''} ${service.user?.last_name ?? ''}`.trim()
</script>

<template>
    <article class="admin-panel admin-recent-panel">
        <header class="admin-panel__header">
            <div><span>{{ $t('adminDashboard.latest') }}</span><h2>{{ $t('adminDashboard.recentServices') }}</h2></div>
            <i class="fas fa-layer-group"></i>
        </header>
        <ul v-if="services.length" class="admin-recent-list">
            <li v-for="service in services" :key="service.id">
                <span class="admin-service-icon"><i :class="service.icon || 'fas fa-screwdriver-wrench'"></i></span>
                <span class="admin-recent-copy">
                    <strong>{{ service.name }}</strong>
                    <small>{{ service.category?.name || $t('adminDashboard.uncategorized') }} · {{ providerName(service) || $t('adminDashboard.unknownProvider') }}</small>
                </span>
                <span :class="['admin-state-badge', service.is_active ? 'is-active' : 'is-inactive']">
                    {{ service.is_active ? $t('adminDashboard.active') : $t('adminDashboard.inactive') }}
                </span>
            </li>
        </ul>
        <p v-else class="admin-empty">{{ $t('adminDashboard.noRecentServices') }}</p>
    </article>
</template>
