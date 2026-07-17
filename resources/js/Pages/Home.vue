<script setup>
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import HeroSearch from '@/Components/HeroSearch.vue'
import MissionCard from '@/Components/MissionCard.vue'
import maliMap from '@/assets/mali-map.svg?raw'
import heroImage from '@/assets/hero-barasira.png'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    categories: { type: Array, default: () => [] },
    cities: { type: Array, default: () => [] },
    randomCategories: { type: Array, default: () => [] },
    missions: { type: Array, default: () => [] },
})

const page = usePage()
const { t } = useI18n()
const selectedCity = ref(null)
const isAuthenticated = computed(() => Boolean(page.props?.auth?.user))
const primaryAction = computed(() =>
    page.props?.auth?.user?.role === 'client' ? '/missions/create' : '/services'
)
const primaryLabel = computed(() =>
    page.props?.auth?.user?.role === 'client' ? t('home.publishMission') : t('home.heroBtnFindService')
)
const cityNames = computed(() => props.cities.map(city => city.name).filter(Boolean))

function onMapClick(event) {
    const city = event.target.closest('.city')
    if (!city) return

    selectedCity.value = city.dataset.city || city.id || null
}
</script>

<template>
    <AppLayout :title="$t('navigation.home')">
        <div class="home-page">
            <section class="home-hero">
                <div class="home-container home-hero__grid">
                    <div class="home-hero__content">
                        <span class="home-kicker">
                            <span>●</span>
                            {{ $t('home.kicker') }}
                        </span>

                        <h1>
                            {{ $t('home.titleStart') }}
                            <em>{{ $t('home.titleAccent') }}</em>
                        </h1>

                        <p>
                            {{ $t('home.description') }}
                        </p>

                        <HeroSearch :categories="categories" :cities="cities" />

                        <div class="home-hero__actions">
                            <Link :href="primaryAction" class="home-button home-button--primary">
                                {{ primaryLabel }}
                                <span aria-hidden="true">→</span>
                            </Link>
                            <Link v-if="!isAuthenticated" href="/register" class="home-button home-button--ghost">
                                {{ $t('home.heroBtnBecomeProvider') }}
                            </Link>
                        </div>

                        <div class="home-hero__trust">
                            <div class="home-avatar-stack" aria-hidden="true">
                                <span>MK</span><span>AD</span><span>FS</span>
                            </div>
                            <p><strong>{{ $t('home.localProfessionals') }}</strong><small>{{ $t('home.availableCities') }}</small></p>
                        </div>
                    </div>

                    <div class="home-hero__visual">
                        <div class="home-hero__image">
                            <img :src="heroImage" :alt="$t('home.heroImageAlt')">
                        </div>
                        <div class="home-floating-card home-floating-card--rating">
                            <span>★</span>
                            <div><strong>{{ $t('home.ratedProfiles') }}</strong><small>{{ $t('home.chooseConfidently') }}</small></div>
                        </div>
                        <div class="home-floating-card home-floating-card--mission">
                            <span>✓</span>
                            <div><strong>{{ $t('home.needPublished') }}</strong><small>{{ $t('home.receiveProposals') }}</small></div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="home-proof">
                <div class="home-container home-proof__grid">
                    <article>
                        <strong>{{ categories.length || '40+' }}</strong>
                        <span>{{ $t('home.proof.categories') }}</span>
                    </article>
                    <article>
                        <strong>{{ cities.length || '8+' }}</strong>
                        <span>{{ $t('home.proof.cities') }}</span>
                    </article>
                    <article>
                        <strong>{{ missions.length }}</strong>
                        <span>{{ $t('home.proof.missions') }}</span>
                    </article>
                    <article>
                        <strong>3</strong>
                        <span>{{ $t('home.proof.languages') }}</span>
                    </article>
                </div>
            </section>

            <section class="home-section home-how">
                <div class="home-container">
                    <header class="home-section__heading">
                        <div>
                            <span class="home-kicker">{{ $t('home.how.kicker') }}</span>
                            <h2>{{ $t('home.how.title') }}</h2>
                        </div>
                        <p>{{ $t('home.how.description') }}</p>
                    </header>

                    <div class="home-steps">
                        <article>
                            <span class="home-step__number">01</span>
                            <div class="home-step__icon">⌕</div>
                            <h3>{{ $t('home.how.step1Title') }}</h3>
                            <p>{{ $t('home.how.step1Description') }}</p>
                        </article>
                        <article>
                            <span class="home-step__number">02</span>
                            <div class="home-step__icon">◎</div>
                            <h3>{{ $t('home.how.step2Title') }}</h3>
                            <p>{{ $t('home.how.step2Description') }}</p>
                        </article>
                        <article>
                            <span class="home-step__number">03</span>
                            <div class="home-step__icon">↗</div>
                            <h3>{{ $t('home.how.step3Title') }}</h3>
                            <p>{{ $t('home.how.step3Description') }}</p>
                        </article>
                    </div>
                </div>
            </section>

            <section v-if="randomCategories.length" class="home-section home-categories">
                <div class="home-container">
                    <header class="home-section__heading home-section__heading--inline">
                        <div>
                            <span class="home-kicker">{{ $t('home.categoriesSection.kicker') }}</span>
                            <h2>{{ $t('home.categoriesSection.title') }}</h2>
                        </div>
                        <Link href="/services" class="home-text-link">{{ $t('home.categoriesSection.viewAll') }} →</Link>
                    </header>

                    <div class="home-category-grid">
                        <Link
                            v-for="(category, index) in randomCategories"
                            :key="category.id"
                            :href="`/services?category=${category.id}`"
                            class="home-category-card"
                        >
                            <span class="home-category-card__icon">
                                <i :class="category.icon || 'bi bi-grid'" />
                            </span>
                            <span>
                                <strong>{{ category.name }}</strong>
                                <small>{{ $t('home.categoriesSection.discover') }}</small>
                            </span>
                            <em>{{ String(index + 1).padStart(2, '0') }}</em>
                        </Link>
                    </div>
                </div>
            </section>

            <section class="home-section home-coverage">
                <div class="home-container home-coverage__grid">
                    <div class="home-coverage__copy">
                        <span class="home-kicker">{{ $t('home.coverage.kicker') }}</span>
                        <h2>{{ $t('home.coverage.title') }}</h2>
                        <p>
                            {{ $t('home.coverage.description') }}
                        </p>
                        <div class="home-city-list">
                            <span v-for="city in cityNames.slice(0, 8)" :key="city">{{ city }}</span>
                        </div>
                        <Link href="/services" class="home-button home-button--primary">{{ $t('home.coverage.action') }}</Link>
                    </div>

                    <div class="home-map-card">
                        <div class="home-map-card__top">
                            <span>{{ $t('home.coverage.national') }}</span>
                            <strong>{{ $t('home.coverage.cityCount', { count: cities.length }) }}</strong>
                        </div>
                        <div class="mali-map" v-html="maliMap" @click="onMapClick" />
                        <p v-if="selectedCity" class="home-map-selection">
                            {{ $t('home.coverage.availableAt') }} <strong>{{ selectedCity }}</strong>
                        </p>
                    </div>
                </div>
            </section>

            <section v-if="missions.length" class="home-section home-missions">
                <div class="home-container">
                    <header class="home-section__heading home-section__heading--inline">
                        <div>
                            <span class="home-kicker">{{ $t('home.missionsSection.kicker') }}</span>
                            <h2>{{ $t('home.missionsSection.title') }}</h2>
                        </div>
                        <Link href="/missions/index" class="home-text-link">{{ $t('home.missionsSection.viewMine') }} →</Link>
                    </header>
                    <div class="missions-grid missions-grid--premium">
                        <article v-for="mission in missions.slice(0, 4)" :key="mission.id" class="modern-mission-card">
                            <MissionCard :mission="mission" />
                        </article>
                    </div>
                </div>
            </section>

            <section class="home-final-cta">
                <div class="home-container">
                    <div class="home-final-cta__card">
                        <div>
                            <span class="home-kicker">{{ $t('home.finalCta.kicker') }}</span>
                            <h2>{{ $t('home.finalCta.title') }}</h2>
                            <p>{{ $t('home.finalCta.description') }}</p>
                        </div>
                        <div class="home-final-cta__actions">
                            <Link :href="isAuthenticated ? primaryAction : '/register'" class="home-button home-button--light">
                                {{ isAuthenticated ? primaryLabel : $t('home.finalCta.createAccount') }}
                            </Link>
                            <Link href="/services" class="home-button home-button--outline-light">{{ $t('home.finalCta.viewServices') }}</Link>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
