<script setup>
import DashboardIcon from '@/Components/DashboardIcon.vue'
defineProps({
    label: { type: String, required: true },
    value: { type: [Number, String], required: true },
    icon: { type: String, required: true },
    tone: { type: String, default: 'green' },
})
</script>

<template>
    <article class="admin-stat-card">
        <span :class="`admin-stat-card__icon admin-stat-card__icon--${tone}`"><DashboardIcon :name="icon" /></span>
        <span class="admin-stat-card__copy"><small>{{ label }}</small><strong>{{ value }}</strong></span>
    </article>
</template>
