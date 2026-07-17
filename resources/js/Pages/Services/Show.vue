<script setup>
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useServiceCategoryLabel } from '@/composables/useServiceCategoryLabel'

const props = defineProps({
    service: { type: Object, required: true },
    providerStats: { type: Object, default: () => ({}) },
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
                        <Link href="/">{{ $t('navigation.home') }}</Link><i class="fas fa-chevron-right"></i>
                        <Link href="/services">{{ $t('navigation.services') }}</Link><i class="fas fa-chevron-right"></i>
                        <span>{{ service.name }}</span>
                    </nav>

                    <div class="service-show-hero__content">
                        <span class="service-show-icon"><i :class="service.icon || service.category?.icon || 'fas fa-screwdriver-wrench'"></i></span>
                        <div>
                            <div class="service-show-badges">
                                <span>{{ service.category?.name ? categoryLabel(service.category.name) : $t('serviceShow.service') }}</span>
                                <span :class="service.is_active ? 'is-active' : 'is-inactive'">{{ service.is_active ? $t('serviceShow.available') : $t('serviceShow.unavailable') }}</span>
                            </div>
                            <h1>{{ service.name }}</h1>
                            <p><i class="fas fa-location-dot"></i>{{ location }}</p>
                        </div>
                        <Link v-if="canAdminEdit" :href="`/admin/services/${service.id}/edit`" class="service-show-admin-edit"><i class="fas fa-pen"></i>{{ $t('serviceShow.editService') }}</Link>
                    </div>
                </div>
            </section>

            <section class="service-show-body">
                <div class="service-show-container service-show-layout">
                    <div class="service-show-main">
                        <section class="service-show-highlights">
                            <article><i class="fas fa-coins"></i><span><small>{{ $t('serviceShow.estimatedPrice') }}</small><strong>{{ priceRange }}</strong></span></article>
                            <article><i class="fas fa-location-dot"></i><span><small>{{ $t('serviceShow.interventionArea') }}</small><strong>{{ location }}</strong></span></article>
                            <article><i class="fas fa-calendar-check"></i><span><small>{{ $t('serviceShow.published') }}</small><strong>{{ publishedDate }}</strong></span></article>
                        </section>

                        <article class="service-show-panel service-show-description">
                            <span class="service-show-section-label">{{ $t('serviceShow.presentation') }}</span>
                            <h2>{{ $t('serviceShow.aboutService') }}</h2>
                            <p>{{ service.description }}</p>
                        </article>

                        <article class="service-show-panel service-show-reassurance">
                            <span class="service-show-reassurance__icon"><i class="fas fa-shield-heart"></i></span>
                            <div><h2>{{ $t('serviceShow.exchangeSafely') }}</h2><p>{{ $t('serviceShow.exchangeSafelyHint') }}</p></div>
                        </article>
                    </div>

                    <aside class="service-show-sidebar">
                        <article class="service-provider-card">
                            <span class="service-provider-card__label">{{ $t('serviceShow.provider') }}</span>
                            <div class="service-provider-card__identity">
                                <span class="service-provider-avatar"><img v-if="service.user?.avatar_url" :src="service.user.avatar_url" alt=""><b v-else>{{ providerInitials || '?' }}</b></span>
                                <div><h2>{{ providerName || $t('serviceShow.provider') }}</h2><p v-if="service.user?.verified"><i class="fas fa-circle-check"></i>{{ $t('serviceShow.verifiedProfile') }}</p></div>
                            </div>

                            <div class="service-provider-rating"><span aria-hidden="true">★</span><strong>{{ rating ? rating.toFixed(1) : $t('serviceShow.newProvider') }}</strong><small v-if="providerStats.reviews">({{ $t('serviceShow.reviews', { count: providerStats.reviews }) }})</small></div>
                            <p v-if="service.user?.bio" class="service-provider-bio">{{ service.user.bio }}</p>
                            <dl class="service-provider-stats"><div><dt>{{ $t('serviceShow.activeServices') }}</dt><dd>{{ providerStats.active_services ?? 0 }}</dd></div><div><dt>{{ $t('serviceShow.rating') }}</dt><dd>{{ rating ? $t('serviceShow.ratingOutOfFive', { rating: rating.toFixed(1) }) : '—' }}</dd></div></dl>

                            <button v-if="canContact" type="button" :disabled="!service.is_active" @click="contactProvider"><i class="fas fa-comment-dots"></i>{{ service.is_active ? $t('serviceShow.contactProvider') : $t('serviceShow.unavailable') }}</button>
                            <Link v-else-if="isGuest" href="/login" class="service-provider-login"><i class="fas fa-arrow-right-to-bracket"></i>{{ $t('serviceShow.loginToContact') }}</Link>
                            <p v-else-if="currentUser?.id === service.user_id" class="service-provider-note">{{ $t('serviceShow.yourService') }}</p>
                            <p v-else class="service-provider-note">{{ $t('serviceShow.clientOnlyContact') }}</p>
                        </article>
                    </aside>
                </div>
            </section>
        </main>
    </AppLayout>
</template>
