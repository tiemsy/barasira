<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import logoUrl from '@/assets/logo-barasira.png'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const page = usePage()
const { locale, t } = useI18n()
const menuOpen = ref(false)
const activeMenu = ref(null)

const user = computed(() => page.props?.auth?.user ?? null)
const currentPath = computed(() => (page.url ?? '/').split('?')[0])
const isAdmin = computed(() => ['admin', 'superadmin'].includes(user.value?.role))
const isSuperAdmin = computed(() => user.value?.role === 'superadmin')
const isClient = computed(() => user.value?.role === 'client')
const isProvider = computed(() => user.value?.role === 'prestataire')
const dashboardUrl = computed(() => {
    if (isAdmin.value) return '/admin/dashboard'
    if (isProvider.value) return '/provider/dashboard'
    return '/dashboard'
})
const roleLabel = computed(() => ({
    admin: t('navbar.roles.admin'),
    superadmin: t('navbar.roles.superadmin'),
    prestataire: t('navbar.roles.provider'),
    client: t('navbar.roles.client'),
}[user.value?.role] ?? t('navbar.roles.user')))
const initials = computed(() => {
    const first = user.value?.firstname?.charAt(0) ?? ''
    const last = user.value?.lastname?.charAt(0) ?? ''
    return `${first}${last}`.toUpperCase() || 'BS'
})

const isActive = path => path === '/'
    ? currentPath.value === '/'
    : currentPath.value.startsWith(path)

function changeLanguage(event) {
    locale.value = event.target.value
    localStorage.setItem('lang', locale.value)
}

function toggleMenu(name) {
    activeMenu.value = activeMenu.value === name ? null : name
}

function closeNavigation() {
    menuOpen.value = false
    activeMenu.value = null
}

function closeOnEscape(event) {
    if (event.key === 'Escape') closeNavigation()
}

watch(() => page.url, closeNavigation)
onMounted(() => document.addEventListener('keydown', closeOnEscape))
onBeforeUnmount(() => document.removeEventListener('keydown', closeOnEscape))
</script>

<template>
    <div class="navbar-container">
        <Link :href="user ? dashboardUrl : '/'" class="navbar-logo" :aria-label="user ? $t('navigation.dashboard') : $t('navbar.homeLabel')" @click="closeNavigation">
            <span class="navbar-logo__mark">
                <img :src="logoUrl" alt="">
            </span>
            <span class="navbar-logo__copy">
                <strong>{{ $t('app.name') }}</strong>
                <small>{{ $t('navbar.tagline') }}</small>
            </span>
        </Link>

        <button
            type="button"
            class="navbar-toggle"
            :class="{ active: menuOpen }"
            :aria-expanded="menuOpen"
            aria-controls="primary-navigation"
            :aria-label="$t('navbar.openMenu')"
            @click="menuOpen = !menuOpen"
        >
            <span />
            <span />
            <span />
        </button>

        <nav id="primary-navigation" class="navbar-links" :class="{ open: menuOpen }" :aria-label="$t('navbar.mainNavigation')">
            <ul class="nav-list">
                <li>
                    <Link href="/" class="nav-link" :class="{ active: isActive('/') }">
                        {{ $t('navigation.home') }}
                    </Link>
                </li>
                <li>
                    <Link href="/services" class="nav-link" :class="{ active: isActive('/services') }">
                        {{ $t('navigation.services') }}
                    </Link>
                </li>

                <template v-if="user">
                    <li v-if="isClient" class="nav-item has-submenu">
                        <button
                            type="button"
                            class="nav-link nav-link--button"
                            :class="{ active: isActive('/missions') }"
                            :aria-expanded="activeMenu === 'missions'"
                            @click.stop="toggleMenu('missions')"
                        >
                            {{ $t('navigation.missions') }}
                            <span class="chevron" aria-hidden="true">⌄</span>
                        </button>
                        <ul class="submenu" :class="{ open: activeMenu === 'missions' }">
                            <li>
                                <Link href="/missions/index" class="submenu-link">
                                    <span class="submenu-icon">⌁</span>
                                    <span><strong>{{ $t('missions.my_missions') }}</strong><small>{{ $t('navbar.trackRequests') }}</small></span>
                                </Link>
                            </li>
                            <li>
                                <Link href="/missions/create" class="submenu-link">
                                    <span class="submenu-icon">＋</span>
                                    <span><strong>{{ $t('missions.create') }}</strong><small>{{ $t('navbar.publishNeed') }}</small></span>
                                </Link>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <Link href="/messages" class="nav-link" :class="{ active: isActive('/messages') }">
                            {{ $t('navigation.messages') }}
                        </Link>
                    </li>
                </template>
                <li><Link href="/partners" class="nav-link" :class="{ active: isActive('/partners') }">{{ $t('navigation.partners') }}</Link></li>
            </ul>

            <div class="navbar-actions">
                <Link
                    href="/contact-us"
                    class="navbar-contact"
                    :class="{ active: isActive('/contact-us') }"
                    @click="closeNavigation"
                >
                    <span class="navbar-contact__icon" aria-hidden="true">
                        <DashboardIcon name="mail" />
                    </span>
                    <span>{{ $t('footer.contactUs') }}</span>
                    <DashboardIcon name="arrow" class="navbar-contact__arrow" />
                </Link>

                <label class="language-control">
                    <span aria-hidden="true">◎</span>
                    <span class="sr-only">{{ $t('navbar.language') }}</span>
                    <select :value="locale" :aria-label="$t('navbar.chooseLanguage')" @change="changeLanguage">
                        <option value="fr">{{ $t('lang.french') }}</option>
                        <option value="bm">{{ $t('lang.bambara') }}</option>
                        <option value="en">{{ $t('lang.english') }}</option>
                    </select>
                </label>

                <template v-if="user">
                    <div class="user-menu">
                        <button
                            type="button"
                            class="user-menu__trigger"
                            :aria-expanded="activeMenu === 'profile'"
                            @click.stop="toggleMenu('profile')"
                        >
                            <span class="user-avatar">{{ initials }}</span>
                            <span class="user-copy">
                                <strong>{{ user.name }}</strong>
                                <small>{{ roleLabel }}</small>
                            </span>
                            <span class="chevron" aria-hidden="true">⌄</span>
                        </button>
                        <ul class="submenu submenu--profile" :class="{ open: activeMenu === 'profile' }">
                            <li>
                                <Link :href="dashboardUrl" class="submenu-link">
                                    <span class="submenu-icon">⌂</span>
                                    <span><strong>{{ $t('navigation.dashboard') }}</strong><small>{{ $t('navbar.overview') }}</small></span>
                                </Link>
                            </li>
                            <li v-if="isSuperAdmin">
                                <Link href="/admin/logs" class="submenu-link">
                                    <span class="submenu-icon"><DashboardIcon name="terminal" /></span>
                                    <span><strong>{{ $t('navbar.systemLogs') }}</strong><small>{{ $t('navbar.systemLogsHint') }}</small></span>
                                </Link>
                            </li>
                            <li v-if="isAdmin"><Link href="/admin/partners" class="submenu-link"><span class="submenu-icon"><DashboardIcon name="building" /></span><span><strong>{{ $t('adminPartners.title') }}</strong><small>{{ $t('adminPartners.navHint') }}</small></span></Link></li>
                            <li v-if="isAdmin"><Link href="/admin/documents" class="submenu-link"><span class="submenu-icon"><DashboardIcon name="certificate" /></span><span><strong>{{ $t('adminDocuments.title') }}</strong><small>{{ $t('adminDocuments.navHint') }}</small></span></Link></li>
                            <li>
                                <Link href="/profile" class="submenu-link">
                                    <span class="submenu-icon">○</span>
                                    <span><strong>{{ $t('navigation.profile') }}</strong><small>{{ $t('navbar.accountInformation') }}</small></span>
                                </Link>
                            </li>
                            <li class="submenu-divider" />
                            <li>
                                <Link href="/logout" method="post" as="button" class="submenu-link submenu-link--danger">
                                    <span class="submenu-icon">↗</span>
                                    <span><strong>{{ $t('navigation.logout') }}</strong></span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </template>

                <template v-else>
                    <Link href="/login" class="navbar-login">{{ $t('navigation.login') }}</Link>
                    <Link href="/register" class="navbar-cta">{{ $t('navigation.register') }}</Link>
                </template>
            </div>
        </nav>
    </div>
</template>
