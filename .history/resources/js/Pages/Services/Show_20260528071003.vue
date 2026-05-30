<script setup>
import { computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue'

import RatingStars from '@/Components/RatingStars.vue'
import UserBadge from '@/Components/UserBadge.vue'

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

    if (
        !props.service.price_min &&
        !props.service.price_max
    ) {
        return 'Prix sur demande'
    }

    if (
        props.service.price_min ===
        props.service.price_max
    ) {
        return `${props.service.price_min} FCFA`
    }

    return `${props.service.price_min} — ${props.service.price_max} FCFA`
})

/* =========================================================
   CONTACT
========================================================= */

const contactProvider = () => {

    router.visit(
        `/messages/create?user=${props.service.user.id}`
    )
}

/* =========================================================
   DATE FORMAT
========================================================= */

const formattedDate = computed(() => {

    if (!props.service.created_at) {
        return null
    }

    return new Date(
        props.service.created_at
    ).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
})
</script>

<template>

    <AppLayout>

        <div class="service-show-page">

            <!-- ===================================================== -->
            <!-- HERO -->
            <!-- ===================================================== -->

            <section class="service-hero">

                <div class="container">

                    <div class="service-hero__content">

                        <span class="service-category">

                            <i class="bi bi-grid"></i>

                            {{ service.category?.name }}

                        </span>

                        <h1 class="service-title">
                            {{ service.name }}
                        </h1>

                        <p class="service-city">

                            <i class="bi bi-geo-alt"></i>

                            {{ service.city?.name ?? 'Mali' }}

                        </p>

                        <!-- STATS -->

                        <div class="service-hero__meta">

                            <div class="service-meta-item">

                                <span class="service-meta-label">
                                    Prix
                                </span>

                                <strong class="service-meta-value">
                                    {{ priceRange }}
                                </strong>

                            </div>

                            <div class="service-meta-item">

                                <span class="service-meta-label">
                                    Disponibilité
                                </span>

                                <strong class="service-meta-value">

                                    <span
                                        :class="[
                                            'service-status',
                                            service.is_active
                                                ? 'active'
                                                : 'inactive'
                                        ]"
                                    >
                                        {{
                                            service.is_active
                                                ? 'Disponible'
                                                : 'Indisponible'
                                        }}
                                    </span>

                                </strong>

                            </div>

                            <div
                                v-if="formattedDate"
                                class="service-meta-item"
                            >

                                <span class="service-meta-label">
                                    Publication
                                </span>

                                <strong class="service-meta-value">
                                    {{ formattedDate }}
                                </strong>

                            </div>

                        </div>

                    </div>

                </div>

            </section>

            <!-- ===================================================== -->
            <!-- CONTENT -->
            <!-- ===================================================== -->

            <section class="service-content">

                <div class="container">

                    <!-- BREADCRUMB -->

                    <nav class="breadcrumb">

                        <ol>

                            <li>
                                <Link href="/">
                                    Accueil
                                </Link>
                            </li>

                            <li>
                                <Link href="/services">
                                    Services
                                </Link>
                            </li>

                            <li>
                                {{ service.name }}
                            </li>

                        </ol>

                    </nav>

                    <!-- GRID -->

                    <div class="grid">

                        <!-- ================================================= -->
                        <!-- MAIN -->
                        <!-- ================================================= -->

                        <div class="service-main">

                            <!-- DESCRIPTION -->

                            <div class="card service-description">

                                <div class="card-header">

                                    <h2>
                                        Description
                                    </h2>

                                </div>

                                <div class="card-body">

                                    <p>
                                        {{ service.description }}
                                    </p>

                                </div>

                            </div>

                            <!-- DETAILS -->

                            <div class="card service-details">

                                <div class="card-header">

                                    <h2>
                                        Détails du service
                                    </h2>

                                </div>

                                <div class="card-body">

                                    <ul class="service-details-list">

                                        <li>

                                            <span>
                                                💰 Prix
                                            </span>

                                            <strong>
                                                {{ priceRange }}
                                            </strong>

                                        </li>

                                        <li>

                                            <span>
                                                📍 Ville
                                            </span>

                                            <strong>
                                                {{
                                                    service.city?.name ??
                                                    'Mali'
                                                }}
                                            </strong>

                                        </li>

                                        <li>

                                            <span>
                                                🗂 Catégorie
                                            </span>

                                            <strong>
                                                {{
                                                    service.category?.name
                                                }}
                                            </strong>

                                        </li>

                                        <li>

                                            <span>
                                                ⚡ Statut
                                            </span>

                                            <strong>

                                                <span
                                                    :class="[
                                                        'service-status',
                                                        service.is_active
                                                            ? 'active'
                                                            : 'inactive'
                                                    ]"
                                                >
                                                    {{
                                                        service.is_active
                                                            ? 'Disponible'
                                                            : 'Indisponible'
                                                    }}
                                                </span>

                                            </strong>

                                        </li>

                                    </ul>

                                </div>

                            </div>

                        </div>

                        <!-- ================================================= -->
                        <!-- SIDEBAR -->
                        <!-- ================================================= -->

                        <aside class="service-sidebar">

                            <!-- PROVIDER -->

                            <div class="card provider-card">

                                <div class="provider-card__top">

                                    <UserBadge
                                        :user="service.user"
                                    />

                                    <RatingStars
                                        :rating="service.user.rating ?? 0"
                                    />

                                </div>

                                <div class="provider-card__infos">

                                    <div class="provider-stat">

                                        <strong>
                                            {{
                                                service.user.services_count ?? 0
                                            }}
                                        </strong>

                                        <span>
                                            Services
                                        </span>

                                    </div>

                                    <div class="provider-stat">

                                        <strong>
                                            {{
                                                service.user.completed_jobs ?? 0
                                            }}
                                        </strong>

                                        <span>
                                            Missions
                                        </span>

                                    </div>

                                    <div class="provider-stat">

                                        <strong>
                                            {{
                                                service.user.rating ?? 0
                                            }}/5
                                        </strong>

                                        <span>
                                            Note
                                        </span>

                                    </div>

                                </div>

                                <button
                                    class="btn-primary btn-block"
                                    @click="contactProvider"
                                >

                                    <i class="bi bi-chat-dots"></i>

                                    Contacter le prestataire

                                </button>

                            </div>

                            <!-- SECURITY -->

                            <div class="card service-security">

                                <h3>
                                    🔒 Paiement sécurisé
                                </h3>

                                <p>
                                    Tous les échanges et paiements
                                    sont sécurisés via Barasira.
                                </p>

                            </div>

                        </aside>

                    </div>

                </div>

            </section>

        </div>

    </AppLayout>

</template>
