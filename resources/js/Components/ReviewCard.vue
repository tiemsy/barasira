<template>
    <div class="bg-white p-4 rounded shadow hover:shadow-md transition mb-4">
        <div class="flex justify-between items-center mb-2">
            <h3 class="font-semibold text-lg">{{ review.reviewer_name }}</h3>
            <RatingStars :rating="review.rating" />
        </div>
        <p class="text-gray-500 text-sm mb-1">{{ review.comment }}</p>
        <p class="text-gray-400 text-xs">{{ formatDate(review.created_at) }}</p>
    </div>
</template>

<script setup>
import RatingStars from '@/Components/RatingStars.vue'
import { defineProps } from 'vue'

defineProps({
    review: {
        type: Object,
        required: true,
        default: () => ({
            reviewer_name: '',
            rating: 0,
            comment: '',
            created_at: new Date()
        })
    }
})

// Formatage natif de la date en FR
function formatDate(date) {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    })
}
</script>


<!-- A inclure dans le fichier cible : <ReviewCard v-for="review in reviews" :key="review.id" :review="review" /> -->
