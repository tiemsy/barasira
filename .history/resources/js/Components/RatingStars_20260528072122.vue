<template>

    <div
        class="rating-stars"
        :aria-label="`${formattedRating} sur 5`"
    >

        <!-- ===================================================== -->
        <!-- STARS -->
        <!-- ===================================================== -->

        <div class="rating-stars__icons">

            <template
                v-for="star in 5"
                :key="star"
            >

                <!-- FULL -->

                <i
                    v-if="star <= fullStars"
                    class="bi bi-star-fill filled"
                ></i>

                <!-- HALF -->

                <i
                    v-else-if="star === fullStars + 1 && hasHalfStar"
                    class="bi bi-star-half half"
                ></i>

                <!-- EMPTY -->

                <i
                    v-else
                    class="bi bi-star empty"
                ></i>

            </template>

        </div>

        <!-- ===================================================== -->
        <!-- VALUE -->
        <!-- ===================================================== -->

        <div class="rating-stars__value">

            <strong>
                {{ formattedRating }}
            </strong>

            <span>
                / 5
            </span>

        </div>

    </div>

</template>

<script setup>
import { computed } from 'vue'

/* =========================================================
   PROPS
========================================================= */

const props = defineProps({
    rating: {
        type: Number,
        default: 0,
    },
})

/* =========================================================
   COMPUTED
========================================================= */

const normalizedRating = computed(() => {

    const value = Number(props.rating)

    if (Number.isNaN(value)) {
        return 0
    }

    return Math.min(
        Math.max(value, 0),
        5
    )
})

const fullStars = computed(() => {
    return Math.floor(normalizedRating.value)
})

const hasHalfStar = computed(() => {

    const decimal =
        normalizedRating.value -
        fullStars.value

    return decimal >= 0.5
})

const formattedRating = computed(() => {
    return normalizedRating.value.toFixed(1)
})
</script>
