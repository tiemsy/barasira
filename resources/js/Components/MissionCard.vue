<template>
    <div class="modern-mission-card__top">
        <span class="modern-mission-card__badge">
            {{ $t('missions.card.open') }}
        </span>

        <span class="modern-mission-card__budget">
            {{ formatPrice(mission.price) }} FCFA
        </span>
    </div>

    <h3 class="modern-mission-card__title">
        {{ mission.title }}
    </h3>

    <p class="modern-mission-card__address">
        <DashboardIcon name="location" />
        {{ mission.address ?? $t('missions.card.addressMissing') }}
    </p>

    <div class="modern-mission-card__footer">
        <Link :href="`/missions/${mission.slug}`" class="btn btn-primary modern-mission-card__btn">
            {{ $t('missions.details') }}
            <DashboardIcon name="arrow" />
        </Link>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import DashboardIcon from '@/Components/DashboardIcon.vue'

/**
 * Props
 */
const props = defineProps({
    mission: {
        type: Object,
        required: true,
    },
})

/**
 * Utilisateur connecté
 */
const { locale } = useI18n()

/**
 * Format FCFA
 */
const formatPrice = (value) => {
    if (!value) return '0'
    return new Intl.NumberFormat(locale.value).format(value)
}
</script>
