<template>
    <AppLayout>
        <!-- Hero -->

        <section class="hero">
            <div class="hero-overlay"></div>
            <div class="hero-content">
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

                    <button class="hero-btn hero-btn--secondary">
                        {{ $t('home.heroBtnBecomeProvider') }}
                    </button>
                </div>
            </div>
        </section>

        <!-- WHY BARASIRA -->
        <section class="section-light">
            <div class="container">
                <div class="feature-grid">
                    <div class="feature-card fade-up delay-2">
                        <div class="feature-icon secure">
                            <i class="bi bi-lock-fill"></i>
                        </div>
                        <h3>{{ $t('home.security') }}</h3>
                        <p>{{ $t('home.securePayments') }}</p>
                    </div>

                    <div class="feature-card fade-up delay-1">
                        <div class="feature-icon speed">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <h3>{{ $t('home.speed') }}</h3>
                        <p>{{ $t('home.serviceFound') }}</p>
                    </div>

                    <div class="feature-card fade-up">
                        <div class="feature-icon trust">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h3>{{ $t('home.trust') }}</h3>
                        <p>{{ $t('home.verifiedProviders') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mali Map -->
        <section :class="['mali-map-section']">
            <h2 :class="['section-title']">{{ $t('home.servicesAvailable') }}</h2>
            <p v-if="selectedCity" class="city-info">
                {{ selectedCity }} — {{ servicesByCity[selectedCity] }} {{ $t('home.services') }}
            </p>
            <div class="mali-map" v-html="maliMap" @click="onMapClick" />
        </section>

        <!-- Catégories -->
        <section class="section section-category">
            <h2 class="section-title">{{ $t('home.popularCategories') }}</h2>

            <div class="categories-grid">
                <div v-for="category in randomCategories" :key="category.id" class="category-card">
                    <div class="category-icon">
                        <i :class="category.icon"></i>
                    </div>
                    <h3>{{ category.name }}</h3>
                </div>
            </div>
        </section>
        <!--<section>
            <h2 class="text-xl font-semibold mb-4 mt-4">Missions récentes</h2>

            <div v-if="missions.length" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <MissionCard v-for="mission in missions" :key="mission.id" :mission="mission" />
            </div>

            <p v-else class="text-gray-500">
                Aucune mission disponible pour le moment.
            </p>
        </section> -->
    </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import HeroSearch from '@/Components/HeroSearch.vue'
// import MissionCard from '@/Components/MissionCard.vue'
import maliMap from '@/assets/mali-map.svg?raw'

defineProps({
    categories: {
        type: Object,
        required: true,
    },
    cities: {
        type: Object,
        required: true,
    },

    randomCategories: {
        type: Object,
        required: true,
    },

    missions: {
        type: Object,
        required: true,
    },
})


const selectedCity = ref(null)

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

function selectCity(city) {
    selectedCity.value = city
}

function onMapClick(e) {
    const cityGroup = e.target.closest('.city')
    if (!cityGroup) return

    //   const city = cityGroup.dataset.city
    const city = cityGroup.id
    console.log(cityGroup.id)
    selectCity(city)
}

const emit = defineEmits(['updateResults'])

const filters = reactive({
    keyword: '',
    category: '',
    city: ''
})

// const search = async () => {
const search = () => {
    const { data } = axios.get('/api/services-search', {
        params: filters
    })
    emit('updateResults', data)
}

</script>
