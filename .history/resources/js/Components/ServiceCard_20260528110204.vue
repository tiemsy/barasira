<template>
    <article class="service-card">
        <!-- COVER -->
        <div class="service-card__cover">
            <img
                v-if="coverImage"
                :src="coverImage"
                :alt="service.name || 'Service Barasira'"
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
                    service.is_active ? 'active' : 'inactive'
                ]"
            >
                <span class="dot"></span>

                {{ service.is_active ? 'Disponible' : 'Indisponible' }}
            </span>
        </div>

        <!-- BODY -->
        <div class="service-card__body">
            <div class="service-card__header">
                <h3 class="service-card__title">
                    {{ service.name || 'Service sans titre' }}
                </h3>

                <div class="service-card__rating">
                    <i class="bi bi-star-fill"></i>
                    {{ rating }}
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
                        {{ service.city?.name ?? 'Mali' }}
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
                    :alt="fullName"
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
                        {{ fullName }}
                    </strong>

                    <span
                        v-if="isVerified"
                        class="verified-text"
                    >
                        <i class="bi bi-patch-check-fill"></i>
                        Prestataire vérifié
                    </span>

                    <span v-else>
                        Prestataire
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

/*
|--------------------------------------------------------------------------
| IMAGE
|--------------------------------------------------------------------------
*/

const coverImage = computed(() => {
    return (
        props.service.image_url ||
        props.service.image ||
        props.service.icon ||
        null
    )
})

/*
|--------------------------------------------------------------------------
| RATING
|--------------------------------------------------------------------------
*/

const rating = computed(() => {
    const value = Number(
        props.service.user?.rating ?? 0
    )

    if (Number.isNaN(value)) {
        return '0.0'
    }

    return value.toFixed(1)
})

/*
|--------------------------------------------------------------------------
| PRICE
|--------------------------------------------------------------------------
*/

const priceRange = computed(() => {
    const min = props.service.price_min
    const max = props.service.price_max

    const hasMin =
        min !== null &&
        min !== undefined &&
        min !== ''

    const hasMax =
        max !== null &&
        max !== undefined &&
        max !== ''

    if (!hasMin && !hasMax) {
        return 'Prix sur demande'
    }

    if (hasMin && !hasMax) {
        return `À partir de ${formatPrice(min)} FCFA`
    }

    if (!hasMin && hasMax) {
        return `Jusqu’à ${formatPrice(max)} FCFA`
    }

    if (Number(min) === Number(max)) {
        return `${formatPrice(min)} FCFA`
    }

    return `${formatPrice(min)} — ${formatPrice(max)} FCFA`
})

/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

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
        .filter(Boolean)
        .map(word => word[0])
        .join('')
        .slice(0, 2)
        .toUpperCase()
})

const isVerified = computed(() => {
    return Boolean(
        props.service.user?.verified ||
        props.service.user?.email_verified_at
    )
})

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

const formatPrice = (value) => {
    return new Intl.NumberFormat(
        'fr-FR'
    ).format(Number(value))
}

const truncate = (
    text,
    length = 120
) => {
    if (!text) {
        return 'Aucune description disponible.'
    }

    return text.length > length
        ? `${text.slice(0, length)}…`
        : text
}
</script>
