<template>
    <div class="bg-white rounded-lg shadow p-4 flex flex-col justify-between">
        <div>
            <!-- Titre -->
            <h3 class="text-lg font-semibold text-gray-800 mb-1">
                {{ mission.title }}
            </h3>

            <!-- Infos -->
            <p class="text-sm text-gray-500 mb-2">
                📍 {{ mission.address ?? 'Non précisée' }}
            </p>

            <p class="text-sm text-gray-600">
                💰 Budget :
                <span class="font-semibold text-orange-600">
                    {{ formatPrice(mission.price) }} FCFA
                </span>
            </p>
        </div>

        <!-- Actions -->
        <div class="mt-4 flex justify-between items-center">
            <Link :href="`/missions/${mission.id}`" class="text-teal-700 font-medium hover:underline">
            Voir détails
            </Link>

            <Link v-if="canApply" :href="`/missions/${mission.id}`"
                class="bg-teal-700 text-white text-sm px-3 py-1 rounded hover:bg-teal-800">
            Postuler
            </Link>
        </div>
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
    return user.value && user.value.role === 'provider'
})

/**
 * Format FCFA
 */
const formatPrice = (value) => {
    if (!value) return '0'
    return new Intl.NumberFormat('fr-FR').format(value)
}
</script>
