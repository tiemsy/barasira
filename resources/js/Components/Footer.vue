<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import logoUrl from '@/assets/logo-barasira.png'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const page = usePage()
const user = computed(() => page.props?.auth?.user ?? null)
const dashboardUrl = computed(() => {
    if (['admin', 'superadmin'].includes(user.value?.role)) return '/admin/dashboard'
    if (user.value?.role === 'prestataire') return '/provider/dashboard'
    return '/dashboard'
})
const currentYear = new Date().getFullYear()

function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' })
}
function openCookiePreferences() {
    window.dispatchEvent(new CustomEvent('barasira:cookie-preferences'))
}
</script>

<template>
    <footer class="footer">
        <div class="footer__accent" aria-hidden="true"></div>

        <div class="footer__container">
            <div class="footer__main">
                <div class="footer__brand">
                    <Link href="/" class="footer__logo" :aria-label="$t('navbar.homeLabel')">
                        <span><img :src="logoUrl" alt=""></span>
                        <strong>{{ $t('app.name') }}</strong>
                    </Link>
                    <p>{{ $t('footer.description') }}</p>
                    <div class="footer__promise">
                        <DashboardIcon name="shield" />
                        <span>{{ $t('footer.promise') }}</span>
                    </div>
                </div>

                <nav class="footer__column" :aria-label="$t('footer.explore')">
                    <h2>{{ $t('footer.explore') }}</h2>
                    <Link href="/">{{ $t('navigation.home') }}</Link>
                    <Link href="/services">{{ $t('navigation.services') }}</Link>
                    <Link href="/partners">{{ $t('navigation.partners') }}</Link>
                    <Link href="/avis">{{ $t('platformReviews.navigation') }}</Link>
                    <Link href="/contact-us">{{ $t('footer.contactUs') }}</Link>
                </nav>

                <nav class="footer__column" :aria-label="$t('footer.account')">
                    <h2>{{ $t('footer.account') }}</h2>
                    <template v-if="user">
                        <Link :href="dashboardUrl">{{ $t('navigation.dashboard') }}</Link>
                        <Link href="/messages">{{ $t('navigation.messages') }}</Link>
                        <Link href="/profile">{{ $t('navigation.profile') }}</Link>
                    </template>
                    <template v-else>
                        <Link href="/login">{{ $t('navigation.login') }}</Link>
                        <Link href="/register">{{ $t('navigation.register') }}</Link>
                    </template>
                </nav>

                <nav class="footer__column footer__legal" :aria-label="$t('legal.documents')">
                    <h2>{{ $t('legal.documents') }}</h2>
                    <Link href="/legal/cgu">{{ $t('legal.links.cgu') }}</Link>
                    <Link href="/legal/cgv">{{ $t('legal.links.cgv') }}</Link>
                    <Link href="/legal/confidentialite">{{ $t('legal.links.confidentialite') }}</Link>
                    <Link href="/legal/cookies">{{ $t('legal.links.cookies') }}</Link>
                    <Link href="/legal/moderation">{{ $t('legal.links.moderation') }}</Link>
                    <Link href="/legal/kyc">{{ $t('legal.links.kyc') }}</Link>
                </nav>

                <div class="footer__contact">
                    <h2>{{ $t('footer.needHelp') }}</h2>
                    <p>{{ $t('footer.helpDescription') }}</p>
                    <Link href="/contact-us" class="footer__contact-link">
                        <span><DashboardIcon name="mail" /></span>
                        <span>
                            <small>{{ $t('footer.writeToUs') }}</small>
                            <strong>{{ $t('footer.contactUs') }}</strong>
                        </span>
                        <DashboardIcon name="arrow" />
                    </Link>
                </div>
            </div>

            <div class="footer__bottom">
                <p>© {{ currentYear }} {{ $t('footer.copyright') }}</p>
                <div>
                    <span>{{ $t('footer.madeForMali') }}</span>
                    <button type="button" class="footer__cookies" @click="openCookiePreferences">{{ $t('cookies.manage') }}</button>
                    <button type="button" :aria-label="$t('footer.backToTop')" @click="scrollToTop">
                        <DashboardIcon name="arrow-up" />
                    </button>
                </div>
            </div>
        </div>
    </footer>
</template>
