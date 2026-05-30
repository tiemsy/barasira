<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import RatingStars from '@/Components/RatingStars.vue'
import UserBadge from '@/Components/UserBadge.vue'
import { computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
    service: {
        type: Object,
        required: true
    }
})

const priceRange = computed(() => {
    if (!props.service.price_min && !props.service.price_max) return 'Prix sur demande'
    if (props.service.price_min === props.service.price_max) {
        return `${props.service.price_min} FCFA`
    }
    return `${props.service.price_min} – ${props.service.price_max} FCFA`
})

const contactProvider = () => {
    router.visit(`/messages/create?user=${props.service.user.id}`)
}
</script>

<template>
    <AppLayout title="Service">

        <!-- HERO SERVICE -->
        <section class="service-hero">
            <span class="service-category">
                {{ service.category?.name }}
            </span>

            <h1 class="service-title">
                {{ service.name }}
            </h1>

            <p class="service-city">
                📍 {{ service.city?.name ?? 'Mali' }}
            </p>
        </section>

        <!-- CONTENT -->
        <section class="service-content">

            <nav class="breadcrumb">
                <Link href="/" class="breadcrumb-link">Accueil</Link>
                <span class="breadcrumb-separator">›</span>

                <Link href="/services" class="breadcrumb-link">Services</Link>
                <span class="breadcrumb-separator">›</span>

                <span class="breadcrumb-current">
                    {{ service.name }}
                </span>
            </nav>
            <div class="container grid">

                <!-- LEFT -->
                <div class="service-main">

                    <div class="card service-description">
                        <h2>Description</h2>
                        <p>{{ service.description }}</p>
                    </div>

                    <div class="card service-details">
                        <h2>Détails</h2>

                        <ul>

                            <li><strong>Prix :</strong> <span class="service-price">💰 {{ priceRange }}</span></li>
                            <li><strong>Disponibilité :</strong> {{ service.is_active ? 'Disponible' : 'Indisponible' }}
                            </li>
                            <li v-if="service.created_at">
                                <strong>Publié :</strong> {{ new Date(service.created_at).toLocaleDateString() }}
                            </li>
                        </ul>
                    </div>

                </div>

                <!-- RIGHT -->
                <aside class="service-sidebar">

                    <div class="card provider-card">
                        <UserBadge :user="service.user" />

                        <RatingStars :rating="service.user.rating ?? 0" />

                        <button class="btn-primary btn-block" @click="contactProvider">
                            Contacter le prestataire
                        </button>
                    </div>

                </aside>

            </div>
        </section>

    </AppLayout>
</template>
