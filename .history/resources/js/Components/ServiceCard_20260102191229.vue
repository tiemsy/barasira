<template>
    <article class="service-card">
        <!-- En-tête -->
        <div class="service-card-header">
            <img v-if="service.icon" :src="service.icon" alt="" class="service-icon" />
            <div class="service-title">
                <h3>{{ service.name }}</h3>
                <span class="service-category">
                    {{ service.category?.name }}
                </span>
            </div>
        </div>

        <!-- Description -->
        <p class="service-description">
            {{ service.description }}
        </p>

        <!-- Infos -->
        <div class="service-meta">
            <span class="service-city">
                📍 {{ service.city?.name ?? 'Non précisé' }}
            </span>

            <span class="service-price">
                💰 {{ service.price_min }} – {{ service.price_max }} FCFA
            </span>
        </div>

        <!-- Prestataire -->
        <div class="service-user">
            <img v-if="service.user?.avatar_url" :src="service.user.avatar_url" class="user-avatar" alt="" />
            <div>
                <strong>{{ service.user?.first_name }}</strong>
                <div class="user-rating">
                    ⭐ {{ service.user?.rating ?? 'N/A' }}
                </div>
            </div>
        </div>

        <!-- Action -->
        <Link :href="`/services/${service.id}`" class="btn btn-primary service-card__btn">
            Voir le service
        </Link>
    </article>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({
    service: {
        type: Object,
        required: true,
    },
})
</script>
