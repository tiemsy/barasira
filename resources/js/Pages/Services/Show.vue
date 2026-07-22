<script setup>
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useServiceCategoryLabel } from '@/composables/useServiceCategoryLabel'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const props = defineProps({
    service: { type: Object, required: true },
    providerStats: { type: Object, default: () => ({}) },
    providerCredentials: { type: Object, default: null },
})

const page = usePage()
const { locale, t } = useI18n()
const { categoryLabel } = useServiceCategoryLabel()
const currentUser = computed(() => page.props?.auth?.user ?? null)
const providerName = computed(() => `${props.service.user?.first_name ?? ''} ${props.service.user?.last_name ?? ''}`.trim())
const providerInitials = computed(() => `${props.service.user?.first_name?.[0] ?? ''}${props.service.user?.last_name?.[0] ?? ''}`.toUpperCase())
const location = computed(() => [props.service.municipality?.name, props.service.city?.name].filter(Boolean).join(', ') || t('serviceShow.mali'))
const canContact = computed(() => currentUser.value?.role === 'client' && currentUser.value.id !== props.service.user_id)
const isGuest = computed(() => !currentUser.value)
const canAdminEdit = computed(() => ['admin', 'superadmin'].includes(currentUser.value?.role))
const rating = computed(() => Number(props.service.user?.rating ?? 0))
const hasCredentials = computed(() => ['educations', 'experiences', 'certifications']
    .some(key => props.providerCredentials?.[key]?.length))

const formatMoney = value => new Intl.NumberFormat(locale.value).format(Number(value ?? 0))
const formatPrice = value => t('serviceShow.currencyAmount', { amount: formatMoney(value) })
const priceRange = computed(() => {
    const minimum = Number(props.service.price_min ?? 0)
    const maximum = Number(props.service.price_max ?? 0)
    if (!minimum && !maximum) return t('serviceShow.priceOnRequest')
    if (minimum === maximum) return formatPrice(minimum)
    return t('serviceShow.priceRange', { minimum: formatPrice(minimum), maximum: formatPrice(maximum) })
})
const publishedDate = computed(() => props.service.created_at
    ? new Intl.DateTimeFormat(locale.value, { day: '2-digit', month: 'long', year: 'numeric' }).format(new Date(props.service.created_at))
    : t('serviceShow.notSpecified'))

function contactProvider() {
    if (canContact.value) router.visit(`/messages/create?user=${props.service.user_id}`)
}
</script>

<template>
    <Head :title="service.name" />
    <AppLayout>
        <main class="service-show-page">
            <section class="service-show-hero">
                <div class="service-show-container">
                    <nav class="service-show-breadcrumb" :aria-label="$t('serviceShow.breadcrumb')">
                        <Link href="/">{{ $t('navigation.home') }}</Link><DashboardIcon name="chevron" />
                        <Link href="/services">{{ $t('navigation.services') }}</Link><DashboardIcon name="chevron" />
                        <span>{{ service.name }}</span>
                    </nav>

                    <div class="service-show-hero__content">
                        <span class="service-show-icon"><DashboardIcon name="tools" /></span>
                        <div>
                            <div class="service-show-badges">
                                <span>{{ service.category?.name ? categoryLabel(service.category.name) : $t('serviceShow.service') }}</span>
                                <span :class="service.is_active ? 'is-active' : 'is-inactive'">{{ service.is_active ? $t('serviceShow.available') : $t('serviceShow.unavailable') }}</span>
                            </div>
                            <h1>{{ service.name }}</h1>
                            <p><DashboardIcon name="location" />{{ location }}</p>
                        </div>
                        <Link v-if="canAdminEdit" :href="`/admin/services/${service.id}/edit`" class="service-show-admin-edit"><DashboardIcon name="edit" />{{ $t('serviceShow.editService') }}</Link>
                    </div>
                </div>
            </section>

            <section class="service-show-body">
                <div class="service-show-container service-show-layout">
                    <div class="service-show-main">
                        <section class="service-show-highlights">
                            <article><span class="service-highlight-icon"><DashboardIcon name="coins" /></span><span><small>{{ $t('serviceShow.estimatedPrice') }}</small><strong>{{ priceRange }}</strong></span></article>
                            <article><span class="service-highlight-icon"><DashboardIcon name="location" /></span><span><small>{{ $t('serviceShow.interventionArea') }}</small><strong>{{ location }}</strong></span></article>
                            <article><span class="service-highlight-icon"><DashboardIcon name="calendar" /></span><span><small>{{ $t('serviceShow.published') }}</small><strong>{{ publishedDate }}</strong></span></article>
                        </section>

                        <article class="service-show-panel service-show-description">
                            <span class="service-show-section-label">{{ $t('serviceShow.presentation') }}</span>
                            <h2>{{ $t('serviceShow.aboutService') }}</h2>
                            <p>{{ service.description }}</p>
                        </article>

                        <article v-if="currentUser?.role === 'client'" class="service-show-panel provider-background">
                            <span class="service-show-section-label">{{ $t('providerCredentials.eyebrow') }}</span>
                            <h2>{{ $t('providerPublicCredentials.title') }}</h2>
                            <p class="provider-background__hint">{{ $t('providerPublicCredentials.hint', { provider: providerName }) }}</p>

                            <div v-if="hasCredentials" class="provider-background__groups">
                                <section v-if="providerCredentials.educations?.length">
                                    <h3><DashboardIcon name="education" />{{ $t('providerCredentials.education.title') }}</h3>
                                    <article v-for="education in providerCredentials.educations" :key="education.id">
                                        <strong>{{ education.degree }}</strong>
                                        <span>{{ education.field }} · {{ education.school_name }}</span>
                                        <small>{{ [education.start_year, education.end_year].filter(Boolean).join(' – ') }}<template v-if="education.city || education.country"> · {{ [education.city, education.country].filter(Boolean).join(', ') }}</template></small>
                                    </article>
                                </section>

                                <section v-if="providerCredentials.experiences?.length">
                                    <h3><DashboardIcon name="experience" />{{ $t('providerCredentials.experience.title') }}</h3>
                                    <article v-for="experience in providerCredentials.experiences" :key="experience.id">
                                        <strong>{{ experience.position }}</strong>
                                        <span>{{ experience.company_name }}</span>
                                        <small>{{ experience.start_date }} – {{ experience.end_date || $t('providerCredentials.current') }}</small>
                                        <p v-if="experience.description">{{ experience.description }}</p>
                                    </article>
                                </section>

                                <section v-if="providerCredentials.certifications?.length">
                                    <h3><DashboardIcon name="certificate" />{{ $t('providerCredentials.certification.title') }}</h3>
                                    <article v-for="certification in providerCredentials.certifications" :key="certification.id">
                                        <strong>{{ certification.name }}</strong>
                                        <span>{{ certification.issuer }}</span>
                                        <small>{{ certification.issue_date }}</small>
                                        <a v-if="certification.credential_url" :href="certification.credential_url" target="_blank" rel="noopener noreferrer">{{ $t('providerPublicCredentials.verify') }} <DashboardIcon name="external" /></a>
                                    </article>
                                </section>
                            </div>
                            <p v-else class="provider-background__empty">{{ $t('providerPublicCredentials.empty') }}</p>
                        </article>

                        <article class="service-show-panel service-show-reassurance">
                            <span class="service-show-reassurance__icon"><DashboardIcon name="shield" /></span>
                            <div><h2>{{ $t('serviceShow.exchangeSafely') }}</h2><p>{{ $t('serviceShow.exchangeSafelyHint') }}</p></div>
                        </article>
                    </div>

                    <aside class="service-show-sidebar">
                        <article class="service-provider-card">
                            <span class="service-provider-card__label">{{ $t('serviceShow.provider') }}</span>
                            <div class="service-provider-card__identity">
                                <span class="service-provider-avatar"><img v-if="service.user?.avatar_url" :src="service.user.avatar_url" alt=""><b v-else>{{ providerInitials || '?' }}</b></span>
                                <div><h2>{{ providerName || $t('serviceShow.provider') }}</h2><p :class="{ 'is-unverified': !service.user?.identity_verified_at }"><DashboardIcon :name="service.user?.identity_verified_at ? 'verified' : 'shield'" />{{ service.user?.identity_verified_at ? $t('serviceShow.verifiedProfile') : $t('serviceShow.unverifiedProfile', 'Profil non vérifié') }}</p></div>
                            </div>

                            <div class="service-provider-rating"><span aria-hidden="true">★</span><strong>{{ rating ? rating.toFixed(1) : $t('serviceShow.newProvider') }}</strong><small v-if="providerStats.reviews">({{ $t('serviceShow.reviews', { count: providerStats.reviews }) }})</small></div>
                            <p v-if="service.user?.bio" class="service-provider-bio">{{ service.user.bio }}</p>
                            <dl class="service-provider-stats"><div><dt>{{ $t('serviceShow.activeServices') }}</dt><dd>{{ providerStats.active_services ?? 0 }}</dd></div><div><dt>{{ $t('serviceShow.rating') }}</dt><dd>{{ rating ? $t('serviceShow.ratingOutOfFive', { rating: rating.toFixed(1) }) : '—' }}</dd></div></dl>

                            <button v-if="canContact" type="button" :disabled="!service.is_active" @click="contactProvider"><DashboardIcon name="messages" />{{ service.is_active ? $t('serviceShow.contactProvider') : $t('serviceShow.unavailable') }}</button>
                            <Link v-else-if="isGuest" href="/login" class="service-provider-login"><DashboardIcon name="login" />{{ $t('serviceShow.loginToContact') }}</Link>
                            <p v-else-if="currentUser?.id === service.user_id" class="service-provider-note">{{ $t('serviceShow.yourService') }}</p>
                            <p v-else class="service-provider-note">{{ $t('serviceShow.clientOnlyContact') }}</p>
                        </article>
                    </aside>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
