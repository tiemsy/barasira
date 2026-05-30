<template>

    <article class="service-card">

        <!-- COVER -->
        <div class="service-card__cover">

            <img
                v-if="service.image || service.icon"
                :src="service.image || service.icon"
                :alt="service.name"
                class="service-card__image"
            />

            <div
                v-else
                class="service-card__placeholder"
            >
                <i class="bi bi-tools"></i>
            </div>

            <!-- CATEGORY -->

            <span
                v-if="service.category?.name"
                class="service-card__category"
            >

                <i class="bi bi-grid"></i>

                {{ service.category.name }}

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

        <!-- BODY -->

        <div class="service-card__body">

            <!-- HEADER -->

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
                            'Mali'
                        }}
                    </span>

                </div>

                <div class="service-card__meta-item">

                    <i class="bi bi-cash-stack"></i>

                    <span class="service-card__price">
                        {{ priceRange }}
                    </span>

                </div>

            </div>

        </div>

        <!-- FOOTER -->

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
                    {{ initials }}
                </div>

                <div class="service-card__user-info">

                    <strong>
                        {{
                            fullName
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

/* PRICE */

const priceRange = computed(() => {

    const min = props.service.price_min
    const max = props.service.price_max

    if (!min && !max) {
        return 'Prix sur demande'
    }

    if (min === max) {
        return `${formatPrice(min)} FCFA`
    }

    return `${formatPrice(min)} — ${formatPrice(max)} FCFA`
})

/* USER */

const fullName = computed(() => {

    const first =
        props.service.user?.first_name ?? ''

    const last =
        props.service.user?.last_name ?? ''

    const name =
        `${first} ${last}`.trim()

    return name || 'Prestataire'
})

const initials = computed(() => {

    return fullName.value
        .split(' ')
        .map(word => word[0])
        .join('')
        .slice(0, 2)
        .toUpperCase()
})

/* HELPERS */

const formatPrice = (value) => {

    if (!value) {
        return '0'
    }

    return new Intl.NumberFormat(
        'fr-FR'
    ).format(value)
}

const truncate = (
    text,
    length = 120
) => {

    if (!text) {
        return ''
    }

    return text.length > length
        ? `${text.slice(0, length)}…`
        : text
}
</script>
