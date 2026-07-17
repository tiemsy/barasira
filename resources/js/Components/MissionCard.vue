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
        <i class="bi bi-geo-alt"></i>
        {{ mission.address ?? $t('missions.card.addressMissing') }}
    </p>

    <div class="modern-mission-card__footer">
        <Link :href="`/missions/${mission.id}`" class="btn btn-primary modern-mission-card__btn">
            {{ $t('missions.details') }}
            <i class="bi bi-arrow-right"></i>
        </Link>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'

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
