<template>
    <AppLayout>
        <div class="home-page">

            <!-- ===================================================== -->
            <!-- HERO -->
            <!-- ===================================================== -->

            <section class="hero">

                <div class="hero-overlay"></div>

                <div class="container">

                    <div class="hero-content">

                        <span class="hero-badge">
                            🚀 {{ $t('home.heroBadge') }}
                        </span>

                        <h1 class="hero-title">
                            {{ $t('home.heroTitle') }}
                        </h1>

                        <p class="hero-subtitle">
                            {{ $t('home.heroSubtitle') }}
                        </p>

                        <div class="hero__actions">

                            <Link href="/services">
                                <button class="hero-btn hero-btn--primary">
                                    {{ $t('home.heroBtnFindService') }}
                                </button>
                            </Link>

                            <Link href="/register">
                                <button class="hero-btn hero-btn--secondary">
                                    {{ $t('home.heroBtnBecomeProvider') }}
                                </button>
                            </Link>

                        </div>

                        <!-- SEARCH -->

                        <div class="hero-filters">

                            <div class="hero-filter">
                                <i class="bi bi-search"></i>

                                <input
                                    v-model="filters.keyword"
                                    type="text"
                                    :placeholder="$t('home.searchPlaceholder')"
                                />
                            </div>

                            <div class="hero-filter">
                                <i class="bi bi-grid"></i>

                                <select v-model="filters.category">
                                    <option value="">
                                        {{ $t('home.allCategories') }}
                                    </option>

                                    <option
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="hero-filter">
                                <i class="bi bi-geo-alt"></i>

                                <select v-model="filters.city">
                                    <option value="">
                                        {{ $t('home.allCities') }}
                                    </option>

                                    <option
                                        v-for="city in cities"
                                        :key="city"
                                        :value="city"
                                    >
                                        {{ city.name }}
                                    </option>
                                </select>
                            </div>

                            <button
                                class="hero-search-btn"
                                @click="search"
                            >
                                <i class="bi bi-search"></i>

                                {{ $t('home.search') }}
                            </button>

                        </div>

                    </div>

                </div>

            </section>

            <!-- ===================================================== -->
            <!-- FEATURES -->
            <!-- ===================================================== -->

            <section class="features-section">

                <div class="container">

                    <div class="section-header">

                        <span class="section-badge">
                            ✨ {{ $t('home.whyBarasira') }}
                        </span>

                        <h2 class="section-title">
                            {{ $t('home.servicesTrusted') }}
                        </h2>

                        <p class="section-subtitle">
                            {{ $t('home.servicesTrustedSubtitle') }}
                        </p>

                    </div>

                    <div class="feature-grid">

                        <!-- SECURITY -->

                        <div class="feature-card fade-up delay-1">

                            <div class="feature-icon secure">
                                <i class="bi bi-lock-fill"></i>
                            </div>

                            <h3>
                                {{ $t('home.security') }}
                            </h3>

                            <p>
                                {{ $t('home.securePayments') }}
                            </p>

                        </div>

                        <!-- SPEED -->

                        <div class="feature-card fade-up delay-2">

                            <div class="feature-icon speed">
                                <i class="bi bi-lightning-charge-fill"></i>
                            </div>

                            <h3>
                                {{ $t('home.speed') }}
                            </h3>

                            <p>
                                {{ $t('home.serviceFound') }}
                            </p>

                        </div>

                        <!-- TRUST -->

                        <div class="feature-card fade-up delay-3">

                            <div class="feature-icon trust">
                                <i class="bi bi-shield-check"></i>
                            </div>

                            <h3>
                                {{ $t('home.trust') }}
                            </h3>

                            <p>
                                {{ $t('home.verifiedProviders') }}
                            </p>

                        </div>

                    </div>

                </div>

            </section>

            <!-- ===================================================== -->
            <!-- STATS -->
            <!-- ===================================================== -->

            <section class="home-stats">

                <div class="container">

                    <div class="home-stats-grid">

                        <div class="home-stat-card">

                            <div class="home-stat-value">
                                2K+
                            </div>

                            <div class="home-stat-label">
                                {{ $t('home.providers') }}
                            </div>

                        </div>

                        <div class="home-stat-card">

                            <div class="home-stat-value">
                                15K+
                            </div>

                            <div class="home-stat-label">
                                {{ $t('home.servicesDone') }}
                            </div>

                        </div>

                        <div class="home-stat-card">

                            <div class="home-stat-value">
                                98%
                            </div>

                            <div class="home-stat-label">
                                {{ $t('home.customerSatisfaction') }}
                            </div>

                        </div>

                        <div class="home-stat-card">

                            <div class="home-stat-value">
                                24/7
                            </div>

                            <div class="home-stat-label">
                                {{ $t('home.support') }}
                            </div>

                        </div>

                    </div>

                </div>

            </section>

            <!-- ===================================================== -->
            <!-- MALI MAP -->
            <!-- ===================================================== -->

            <section class="mali-map-section">

                <div class="container">

                    <div class="section-header">

                        <span class="section-badge">
                            📍 {{ $t('home.coverage') }}
                        </span>

                        <h2 class="section-title">
                            {{ $t('home.servicesAvailable') }}
                        </h2>

                    </div>

                    <p
                        v-if="selectedCity"
                        class="city-info"
                    >
                        {{ selectedCity }}
                        —
                        {{ servicesByCity[selectedCity] || 0 }}
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

            <!-- ===================================================== -->
            <!-- CATEGORIES -->
            <!-- ===================================================== -->

            <section class="section section-category">

                <div class="container">

                    <div class="section-header">

                        <span class="section-badge">
                            🔥 {{ $t('home.topCategories') }}
                        </span>

                        <h2 class="section-title">
                            {{ $t('home.popularCategories') }}
                        </h2>

                    </div>

                    <div class="categories-grid">

                        <div
                            v-for="category in randomCategories"
                            :key="category.id"
                            class="category-card"
                        >

                            <div class="category-icon">
                                <i :class="category.icon"></i>
                            </div>

                            <h3>
                                {{ category.name }}
                            </h3>

                        </div>

                    </div>

                </div>

            </section>

        </div>
    </AppLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { Link } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue'

import axios from 'axios'

import maliMap from '@/assets/mali-map.svg?raw'

const props = defineProps({
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
        required: false,
        default: () => [],
    },
})

const selectedCity = ref(null)

const servicesByCity = {
    bamako: 128,
    sikasso: 54,
    kayes: 32,
    kita: 12,
    mopti: 41,
    tombouctou: 2,
    gao: 8,
    segou: 16,
}

const filters = reactive({
    keyword: '',
    category: '',
    city: '',
})

const selectCity = (city) => {
    selectedCity.value = city
}

const onMapClick = (e) => {

    const cityGroup = e.target.closest('.city')

    if (!cityGroup) {
        return
    }

    selectCity(cityGroup.id)
}

const search = async () => {

    try {

        const response = await axios.get('/api/services-search', {
            params: filters,
        })

        console.log(response.data)

    } catch (error) {

        console.error(error)
    }
}
</script>
