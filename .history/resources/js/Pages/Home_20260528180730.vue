<template>
    <AppLayout>
        <div class="home-page">

            <!-- HERO -->
            <section class="hero">
                <div class="hero-overlay"></div>

                <div class="container">
                    <div class="hero-content">

                        <span class="hero-badge">
                            🇲🇱 Barasira — services de confiance au Mali
                        </span>

                        <h1 class="hero-title">
                            {{ $t('home.heroTitle') }}
                        </h1>

                        <p class="hero-subtitle">
                            {{ $t('home.heroSubtitle') }}
                        </p>

                        <HeroSearch
                            :categories="categories"
                            :cities="cities"
                            @updateResults="handleSearchResults"
                        />

                        <div class="hero__actions">
                            <Link
                                href="/services"
                                class="hero-btn hero-btn-primary"
                            >
                                {{ $t('home.heroBtnFindService') }}
                            </Link>

                            <Link
                                href="/register"
                                class="hero-btn hero-btn-outline"
                            >
                                {{ $t('home.heroBtnBecomeProvider') }}
                            </Link>
                        </div>

                        <div class="hero-stats">

                            <div class="hero-stat">
                                <strong>2 000+</strong>
                                <span>Prestataires</span>
                            </div>

                            <div class="hero-stat">
                                <strong>15 000+</strong>
                                <span>Services réalisés</span>
                            </div>

                            <div class="hero-stat">
                                <strong>98%</strong>
                                <span>Satisfaction client</span>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

            <!-- WHY BARASIRA -->
            <section class="features-section">
                <div class="container">

                    <div class="section-header">
                        <span class="section-badge">
                            Pourquoi Barasira ?
                        </span>

                        <h2 class="section-title">
                            Une plateforme simple, rapide et sécurisée
                        </h2>

                        <p class="section-subtitle">
                            Trouvez facilement un prestataire fiable ou proposez vos services partout au Mali.
                        </p>
                    </div>

                    <div class="feature-grid">

                        <div class="feature-card fade-up">
                            <div class="feature-icon secure">
                                <i class="bi bi-lock-fill"></i>
                            </div>

                            <h3>{{ $t('home.security') }}</h3>

                            <p>
                                {{ $t('home.securePayments') }}
                            </p>
                        </div>

                        <div class="feature-card fade-up delay-1">
                            <div class="feature-icon speed">
                                <i class="bi bi-lightning-charge-fill"></i>
                            </div>

                            <h3>{{ $t('home.speed') }}</h3>

                            <p>
                                {{ $t('home.serviceFound') }}
                            </p>
                        </div>

                        <div class="feature-card fade-up delay-2">
                            <div class="feature-icon trust">
                                <i class="bi bi-shield-check"></i>
                            </div>

                            <h3>{{ $t('home.trust') }}</h3>

                            <p>
                                {{ $t('home.verifiedProviders') }}
                            </p>
                        </div>

                    </div>
                </div>
            </section>

            <!-- CATEGORIES -->
            <section class="section section-category">
                <div class="container">

                    <div class="section-header">
                        <span class="section-badge">
                            Services populaires
                        </span>

                        <h2 class="section-title">
                            {{ $t('home.popularCategories') }}
                        </h2>

                        <p class="section-subtitle">
                            Explorez les catégories les plus demandées.
                        </p>
                    </div>

                    <div class="categories-grid">

                        <Link
                            v-for="category in randomCategories"
                            :key="category.id"
                            :href="`/services?category=${category.id}`"
                            class="category-card"
                        >
                            <div class="icon">
                                <i :class="category.icon || 'bi bi-grid'"></i>
                            </div>

                            <h3>
                                {{ category.name }}
                            </h3>
                        </Link>

                    </div>
                </div>
            </section>

            <!-- MALI MAP -->
            <section class="mali-map-section">
                <div class="container">

                    <div class="section-header">
                        <span class="section-badge">
                            Couverture nationale
                        </span>

                        <h2 class="section-title">
                            {{ $t('home.servicesAvailable') }}
                        </h2>

                        <p class="section-subtitle">
                            Découvrez les services disponibles dans votre ville.
                        </p>
                    </div>

                    <p
                        v-if="selectedCity"
                        class="city-info"
                    >
                        {{ selectedCity }}
                        —
                        {{ servicesByCity[selectedCity] ?? 0 }}
                        {{ $t('home.services') }}
                    </p>

                    <div class="mali-map-wrapper">
                        <div
                            class="mali-map"
                            v-html="maliMap"
                            @click="onMapClick"
                        />
                    </div>

                </div>
            </section>

            <!-- MISSIONS -->
            <section
                v-if="missions.length"
                class="section home-missions"
            >
                <div class="container">

                    <div class="section-header">
                        <span class="section-badge">
                            Missions récentes
                        </span>

                        <h2 class="section-title">
                            Opportunités disponibles
                        </h2>
                    </div>

                    <div class="missions-grid">

                        <MissionCard
                            v-for="mission in missions.slice(0, 4)"
                            :key="mission.id"
                            :mission="mission"
                        />

                    </div>

                    <div class="home-section-action">
                        <Link
                            href="/missions"
                            class="btn btn-primary"
                        >
                            Voir toutes les missions
                        </Link>
                    </div>

                </div>
            </section>

            <!-- CTA -->
            <section class="home-cta">
                <div class="container">

                    <div class="home-cta-card">

                        <div>
                            <span class="section-badge">
                                Commencez maintenant
                            </span>

                            <h2>
                                Besoin d’un service ou envie de proposer vos compétences ?
                            </h2>

                            <p>
                                Rejoignez Barasira et trouvez rapidement la bonne personne.
                            </p>
                        </div>

                        <div class="home-cta-actions">

                            <Link
                                href="/services"
                                class="btn btn-primary"
                            >
                                Trouver un service
                            </Link>

                            <Link
                                href="/register"
                                class="btn btn-outline"
                            >
                                Devenir prestataire
                            </Link>

                        </div>
                    </div>

                </div>
            </section>

        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue'
import HeroSearch from '@/Components/HeroSearch.vue'
import MissionCard from '@/Components/MissionCard.vue'

import maliMap from '@/assets/mali-map.svg?raw'

defineProps({
    categories: {
        type: Array,
        required: true,
    },

    cities: {
        type: Array,
        required: true,
    },

    randomCategories: {
        type: Array,
        required: true,
    },

    missions: {
        type: Array,
        default: () => [],
    },
})

const selectedCity = ref(null)

const services = ref([])

const servicesByCity = {
    bamako: 128,
    sikasso: 54,
    kayes: 32,
    kita: 0,
    mopti: 41,
    tombouctou: 2,
    gao: 0,
    segou: 16,
}

const handleSearchResults = (results) => {
    services.value = results
}

const onMapClick = (event) => {
    const cityGroup =
        event.target.closest('.city')

    if (!cityGroup) {
        return
    }

    selectedCity.value =
        cityGroup.dataset.city ||
        cityGroup.id
}
</script>
