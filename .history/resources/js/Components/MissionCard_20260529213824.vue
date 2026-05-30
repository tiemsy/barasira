<template>
    <div class="modern-mission-card__top">
        <span class="modern-mission-card__badge">
            Mission ouverte
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
        {{ mission.address ?? 'Adresse non précisée' }}
    </p>

    <div class="modern-mission-card__footer">
        <Link :href="`/missions/${mission.id}`" class="btn btn-primary modern-mission-card__btn">
            Voir détails
            <i class="bi bi-arrow-right"></i>
        </Link>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

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
const page = usePage()
const user = computed(() => page.props.auth?.user ?? null)

/**
 * Peut postuler ?
 * (exemple simple : prestataire connecté)
 */
const canApply = computed(() => {
    return user.value && user.value.role === 'prestataire'
})

/**
 * Format FCFA
 */
const formatPrice = (value) => {
    if (!value) return '0'
    return new Intl.NumberFormat('fr-FR').format(value)
}
</script>
