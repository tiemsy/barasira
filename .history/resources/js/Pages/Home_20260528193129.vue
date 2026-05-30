<template>
    <AppLayout>
        <div class="home-page">

            <!-- HERO -->
            <section class="hero hero-split">
                <div class="container hero-grid">

                    <!-- LEFT -->
                    <div class="hero-left">

                        <span class="hero-badge">
                            🇲🇱 Trouvez un prestataire partout au Mali
                        </span>

                        <h1 class="hero-title">
                            {{ $t('home.heroTitle') }}
                        </h1>

                        <p class="hero-subtitle">
                            {{ $t('home.heroSubtitle') }}
                        </p>

                        <!-- <HeroSearch
                            :categories="categories"
                            :cities="cities"
                            @updateResults="handleSearchResults"
                        /> -->

                        <div class="hero-actions">

                            <Link href="/services" class="btn btn-primary">
                                Trouver un service
                            </Link>

                            <Link href="/register" class="btn btn-outline">
                                Devenir prestataire
                            </Link>

                        </div>

                    </div>

                    <!-- RIGHT -->
                    <div class="hero-right">

                        <div class="hero-map-card">

                            <div class="mali-map" v-html="maliMap" @click="onMapClick" />

                            <div v-if="selectedCity" class="hero-city-card">
                                <strong>
                                    {{ selectedCity }}
                                </strong>

                                <span>
                                    {{ servicesByCity[selectedCity] ?? 0 }}
                                    services
                                </span>
                            </div>

                        </div>

                    </div>

                </div>
            </section>

            <!-- STATS -->
            <section class="home-stats">
                <div class="container">

                    <div class="home-stats-grid">

                        <div class="stats-card">
                            <strong>2 000+</strong>
                            <span>Prestataires actifs</span>
                        </div>

                        <div class="stats-card">
                            <strong>40+</strong>
                            <span>Catégories</span>
                        </div>

                        <div class="stats-card">
                            <strong>15 000+</strong>
                            <span>Demandes traitées</span>
                        </div>

                        <div class="stats-card">
                            <strong>98%</strong>
                            <span>Satisfaction</span>
                        </div>

                    </div>

                </div>
            </section>


            <!-- WHY -->
            <section class="section-light">
                <div class="container">

                    <div class="feature-grid">

                        <div class="feature-card">

                            <div class="feature-icon trust">
                                <i class="bi bi-shield-check"></i>
                            </div>

                            <h3>
                                Prestataires vérifiés
                            </h3>

                            <p>
                                Des profils vérifiés et évalués.
                            </p>

                        </div>

                        <div class="feature-card">

                            <div class="feature-icon speed">
                                <i class="bi bi-lightning-fill"></i>
                            </div>

                            <h3>
                                Rapide
                            </h3>

                            <p>
                                Trouvez un service en quelques minutes.
                            </p>

                        </div>

                        <div class="feature-card">

                            <div class="feature-icon secure">
                                <i class="bi bi-lock-fill"></i>
                            </div>

                            <h3>
                                Sécurisé
                            </h3>

                            <p>
                                Paiements et échanges sécurisés.
                            </p>

                        </div>

                    </div>

                </div>
            </section>

            <!-- MISSIONS -->
            <section v-if="missions.length" class="section">
                <div class="container">

                    <div class="section-header">

                        <!-- <span class="section-badge">
                            Missions
                        </span> -->

                        <h2 class="section-title">
                            Dernières opportunités
                        </h2>

                    </div>

                    <div class="missions-grid">

                        <MissionCard
                            v-for="mission in missions.slice(0, 4)"
                            :key="mission.id"
                            :mission="mission"
                        />

                    </div>

                </div>
            </section>

 <!-- CATEGORIES -->
            <section class="section-category">
                <div class="container">

                    <div class="section-header">

                        <!-- <span class="section-badge">
                            Explorer
                        </span> -->

                        <h2 class="section-title">
                            Catégories populaires
                        </h2>

                    </div>

                    <div class="categories-grid">

                        <Link v-for="category in randomCategories" :key="category.id"
                            :href="`/services?category=${category.id}`" class="category-card">

                            <div class="icon">
                                <i :class="category.icon ||
                                    'bi bi-grid'
                                    "></i>
                            </div>

                            <h3>
                                {{ category.name }}
                            </h3>

                        </Link>

                    </div>

                </div>
            </section>
            <!-- CTA -->
            <section class="home-cta">
                <div class="container">

                    <div class="home-cta-card">

                        <h2>
                            Lancez votre activité avec Barasira
                        </h2>

                        <p>
                            Trouvez vos premiers clients rapidement.
                        </p>

                        <Link href="/register" class="btn btn-primary">
                            Créer mon compte
                        </Link>

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
    categories: Array,
    cities: Array,
    randomCategories: Array,
    missions: Array,
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
