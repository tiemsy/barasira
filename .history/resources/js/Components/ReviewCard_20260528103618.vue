<template>
    <article class="review-card">

        <!-- HEADER -->
        <div class="review-card__header">

            <div class="review-card__user">

                <div class="review-card__avatar">
                    {{ initials }}
                </div>

                <div>
                    <h3 class="review-card__name">
                        {{ review.reviewer_name || 'Utilisateur' }}
                    </h3>

                    <p class="review-card__date">
                        {{ formattedDate }}
                    </p>
                </div>

            </div>

            <RatingStars :rating="review.rating ?? 0" />

        </div>

        <!-- COMMENT -->
        <p class="review-card__comment">
            {{ review.comment || 'Aucun commentaire.' }}
        </p>

    </article>
</template>

<script setup>
import { computed } from 'vue'
import RatingStars from '@/Components/RatingStars.vue'

const props = defineProps({
    review: {
        type: Object,
        required: true,
    },
})

const initials = computed(() => {
    const name = props.review.reviewer_name || 'Utilisateur'

    return name
        .split(' ')
        .map(part => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase()
})

const formattedDate = computed(() => {
    if (!props.review.created_at) {
        return ''
    }

    return new Date(props.review.created_at).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    })
})
</script>
