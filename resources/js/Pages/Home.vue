<script setup>
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import HeroSearch from '@/Components/HeroSearch.vue'
import MissionCard from '@/Components/MissionCard.vue'
import maliMap from '@/assets/mali-map.svg?raw'
import heroImage from '@/assets/hero-barasira.png'
import { useI18n } from 'vue-i18n'
import DashboardIcon from '@/Components/DashboardIcon.vue'
import { serviceIconName } from '@/composables/useServiceIcon'

const props = defineProps({
    categories: { type: Array, default: () => [] },
    cities: { type: Array, default: () => [] },
    randomCategories: { type: Array, default: () => [] },
    missions: { type: Array, default: () => [] },
    partners: { type: Array, default: () => [] },
    featuredPartners: { type: Array, default: () => [] },
    platformReviews: { type: Array, default: () => [] },
    platformReviewStats: { type: Object, default: () => ({ average: 0, count: 0 }) },
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
            <section v-if="featuredPartners.length" class="home-featured-partners">
                <div class="home-container">
                    <div class="home-featured-partners__heading"><span>{{ $t('partners.featuredEyebrow') }}</span><p>{{ $t('partners.featuredText') }}</p></div>
                    <div class="home-featured-partners__grid">
                        <article v-for="partner in featuredPartners" :key="partner.id" class="home-featured-partner">
                            <a :href="partner.website_url || '/partners'" :target="partner.website_url ? '_blank' : undefined" :rel="partner.website_url ? 'noopener noreferrer' : undefined" class="home-featured-partner__logo"><img v-if="partner.logo_url" :src="partner.logo_url" :alt="partner.company_name"><span v-else>{{ partner.company_name }}</span></a>
                            <div><span class="home-featured-partner__badge">{{ $t('partners.featuredBadge') }}</span><h2>{{ partner.company_name }}</h2><p v-if="partner.description">{{ partner.description }}</p><a v-if="partner.website_url" :href="partner.website_url" target="_blank" rel="noopener noreferrer">{{ $t('partners.visitWebsite') }} →</a></div>
                        </article>
                    </div>
                </div>
            </section>
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
                                <DashboardIcon :name="serviceIconName({ category })" />
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

            <section v-if="partners.length" class="home-section home-partners">
                <div class="home-container">
                    <header class="home-section__heading home-section__heading--inline">
                        <div><span class="home-kicker">{{ $t('partners.eyebrow') }}</span><h2>{{ $t('partners.homeTitle') }}</h2></div>
                        <Link href="/partners" class="home-text-link">{{ $t('partners.viewAll') }} →</Link>
                    </header>
                    <div class="home-partners__grid">
                        <a v-for="partner in partners" :key="partner.id" :href="partner.website_url || undefined" :target="partner.website_url ? '_blank' : undefined" :rel="partner.website_url ? 'noopener noreferrer' : undefined" class="home-partner-logo" :aria-label="partner.company_name">
                            <img v-if="partner.logo_url" :src="partner.logo_url" :alt="partner.company_name"><span v-else>{{ partner.company_name }}</span>
                        </a>
                    </div>
                </div>
            </section>

            <section v-if="platformReviews.length" class="home-section home-platform-reviews">
                <div class="home-container">
                    <header class="home-section__heading home-section__heading--inline">
                        <div>
                            <span class="home-kicker">{{ $t('platformReviews.eyebrow') }}</span>
                            <h2>{{ $t('platformReviews.homeTitle') }}</h2>
                            <p>{{ $t('platformReviews.homeSubtitle') }}</p>
                        </div>
                        <div class="home-platform-reviews__summary">
                            <strong>★ {{ platformReviewStats.average }}/5</strong>
                            <span>{{ $t('platformReviews.count', { count: platformReviewStats.count }) }}</span>
                        </div>
                    </header>
                    <div class="home-platform-reviews__grid">
                        <article v-for="review in platformReviews" :key="review.id">
                            <div class="home-platform-review__top">
                                <span class="home-platform-review__avatar">
                                    <img v-if="review.user.avatar_url" :src="review.user.avatar_url" :alt="review.user.first_name">
                                    <template v-else>{{ review.user.first_name?.charAt(0) }}{{ review.user.last_name?.charAt(0) }}</template>
                                </span>
                                <div>
                                    <strong>{{ review.user.first_name }} {{ review.user.last_name?.charAt(0) }}.</strong>
                                    <small>{{ $t(`platformReviews.roles.${review.user.role}`) }}</small>
                                </div>
                                <span class="home-platform-review__rating" :aria-label="`${review.rating}/5`">★ {{ review.rating }}/5</span>
                            </div>
                            <p>“{{ review.comment }}”</p>
                        </article>
                    </div>
                    <div class="home-platform-reviews__action">
                        <Link href="/avis" class="home-button home-button--primary">{{ $t('platformReviews.viewAll') }} →</Link>
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
