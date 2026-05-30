<template>

    <article class="service-card">

        <!-- ===================================================== -->
        <!-- COVER -->
        <!-- ===================================================== -->

        <div class="service-card__cover">

            <img
                v-if="service.image"
                :src="service.image"
                :alt="service.name"
                class="service-card__image"
            />

            <div
                v-else
                class="service-card__placeholder"
            >
                <i class="bi bi-image"></i>
            </div>

            <!-- CATEGORY -->

            <span class="service-card__category">

                <i class="bi bi-grid"></i>

                {{ service.category?.name }}

            </span>

            <!-- STATUS -->

            <span
                :class="[
                    'service-card__status',
                    service.is_active
                        ? 'active'
                        : 'inactive'
                ]"
            >

                <span class="dot"></span>

                {{
                    service.is_active
                        ? 'Disponible'
                        : 'Indisponible'
                }}

            </span>

        </div>

        <!-- ===================================================== -->
        <!-- BODY -->
        <!-- ===================================================== -->

        <div class="service-card__body">

            <!-- TITLE -->

            <div class="service-card__header">

                <h3 class="service-card__title">
                    {{ service.name }}
                </h3>

                <div class="service-card__rating">

                    <i class="bi bi-star-fill"></i>

                    {{ service.user?.rating ?? '0.0' }}

                </div>

            </div>

            <!-- DESCRIPTION -->

            <p class="service-card__description">

                {{ truncate(service.description, 120) }}

            </p>

            <!-- META -->

            <div class="service-card__meta">

                <div class="service-card__meta-item">

                    <i class="bi bi-geo-alt"></i>

                    <span>
                        {{
                            service.city?.name ??
                            'Non précisé'
                        }}
                    </span>

                </div>

                <div class="service-card__meta-item">

                    <i class="bi bi-cash-stack"></i>

                    <span>
                        {{ priceRange }}
                    </span>

                </div>

            </div>

        </div>

        <!-- ===================================================== -->
        <!-- FOOTER -->
        <!-- ===================================================== -->

        <div class="service-card__footer">

            <!-- USER -->

            <div class="service-card__user">

                <img
                    v-if="service.user?.avatar_url"
                    :src="service.user.avatar_url"
                    :alt="service.user?.first_name"
                    class="service-card__avatar"
                />

                <div
                    v-else
                    class="service-card__avatar service-card__avatar--placeholder"
                >
                    <i class="bi bi-person"></i>
                </div>

                <div class="service-card__user-info">

                    <strong>
                        {{
                            service.user?.first_name ??
                            'Prestataire'
                        }}
                    </strong>

                    <span>
                        Prestataire vérifié
                    </span>

                </div>

            </div>

            <!-- ACTION -->

            <Link
                :href="`/services/${service.id}`"
                class="btn btn-primary service-card__btn"
            >

                Voir le service

                <i class="bi bi-arrow-right"></i>

            </Link>

        </div>

    </article>

</template>

<script setup>
import { computed } from 'vue'

import { Link } from '@inertiajs/vue3'

const props = defineProps({
    service: {
        type: Object,
        required: true,
    },
})

/* =========================================================
   PRICE RANGE
========================================================= */

const priceRange = computed(() => {

    const min = props.service.price_min
    const max = props.service.price_max

    if (!min && !max) {
        return 'Prix sur demande'
    }

    if (min === max) {
        return `${min} FCFA`
    }

    return `${min} — ${max} FCFA`
})

/* =========================================================
   TRUNCATE
========================================================= */

const truncate = (text, length = 100) => {

    if (!text) {
        return ''
    }

    return text.length > length
        ? `${text.slice(0, length)}...`
        : text
}
</script>
