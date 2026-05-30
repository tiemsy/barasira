<script setup>
import { computed, ref } from 'vue'

import {
    Link,
    router,
    usePage
} from '@inertiajs/vue3'

import { useI18n } from 'vue-i18n'

/* =========================================================
   I18N
========================================================= */

const { locale } = useI18n()

const changeLang = (lang) => {

    locale.value = lang

    localStorage.setItem(
        'lang',
        lang
    )
}

/* =========================================================
   MENU
========================================================= */

const menuOpen = ref(false)

const activeSubmenu = ref(null)

const toggleSubmenu = (name) => {

    activeSubmenu.value =
        activeSubmenu.value === name
            ? null
            : name
}

const closeMobileMenu = () => {

    menuOpen.value = false

    activeSubmenu.value = null
}

/* =========================================================
   USER
========================================================= */

const page = usePage()

const user = computed(() => {
    return page.props.auth?.user ?? null
})

const isAdmin = computed(() => {
    return user.value?.role === 'admin'
})

const isClient = computed(() => {
    return user.value?.role === 'client'
})

const isProvider = computed(() => {
    return user.value?.role === 'prestataire'
})

/* =========================================================
   LOGOUT
========================================================= */

const logout = () => {

    router.post('/logout')
}
</script>

<template>

    <div class="container navbar-container">

        <!-- ================================================= -->
        <!-- LOGO -->
        <!-- ================================================= -->

        <Link
            href="/"
            class="navbar-logo"
            @click="closeMobileMenu"
        >

            <img
                src="/images/logo-barasira.png"
                alt="Barasira"
            />

            <span>
                Barasira
            </span>

        </Link>

        <!-- ================================================= -->
        <!-- NAVIGATION -->
        <!-- ================================================= -->

        <nav
            :class="[
                'navbar-links',
                { open: menuOpen }
            ]"
        >

            <ul class="nav-list">

                <!-- HOME -->

                <li>

                    <Link
                        href="/"
                        class="nav-link"
                        @click="closeMobileMenu"
                    >

                        {{ $t('navigation.home') }}

                    </Link>

                </li>

                <!-- SERVICES -->

                <li>

                    <Link
                        href="/services"
                        class="nav-link"
                        @click="closeMobileMenu"
                    >

                        {{ $t('navigation.services') }}

                    </Link>

                </li>

                <!-- MISSIONS -->

                <li
                    v-if="user"
                    class="nav-item has-submenu"
                >

                    <button
                        type="button"
                        class="nav-link submenu-trigger"
                        @click="toggleSubmenu('missions')"
                    >

                        <span>
                            {{ $t('navigation.missions') }}
                        </span>

                        <i
                            :class="[
                                'bi',
                                activeSubmenu === 'missions'
                                    ? 'bi-chevron-up'
                                    : 'bi-chevron-down'
                            ]"
                        ></i>

                    </button>

                    <ul
                        :class="[
                            'submenu',
                            {
                                open:
                                    activeSubmenu ===
                                    'missions'
                            }
                        ]"
                    >

                        <li>

                            <Link
                                href="/missions"
                                class="submenu-link"
                                @click="closeMobileMenu"
                            >

                                <i class="bi bi-list-task"></i>

                                Missions

                            </Link>

                        </li>

                        <li
                            v-if="isClient"
                        >

                            <Link
                                href="/missions/create"
                                class="submenu-link"
                                @click="closeMobileMenu"
                            >

                                <i class="bi bi-plus-circle"></i>

                                Créer une mission

                            </Link>

                        </li>

                        <li
                            v-if="isProvider"
                        >

                            <Link
                                href="/missions/applications"
                                class="submenu-link"
                                @click="closeMobileMenu"
                            >

                                <i class="bi bi-send-check"></i>

                                Mes candidatures

                            </Link>

                        </li>

                    </ul>

                </li>

                <!-- PROFILE -->

                <li
                    v-if="user"
                    class="nav-item has-submenu"
                >

                    <button
                        type="button"
                        class="nav-link submenu-trigger"
                        @click="toggleSubmenu('profile')"
                    >

                        <span>
                            {{ $t('navigation.profile') }}
                        </span>

                        <i
                            :class="[
                                'bi',
                                activeSubmenu === 'profile'
                                    ? 'bi-chevron-up'
                                    : 'bi-chevron-down'
                            ]"
                        ></i>

                    </button>

                    <ul
                        :class="[
                            'submenu',
                            {
                                open:
                                    activeSubmenu ===
                                    'profile'
                            }
                        ]"
                    >

                        <li
                            v-if="isAdmin"
                        >

                            <Link
                                href="/admin/dashboard"
                                class="submenu-link"
                                @click="closeMobileMenu"
                            >

                                <i class="bi bi-speedometer2"></i>

                                Dashboard Admin

                            </Link>

                        </li>

                        <li
                            v-if="isProvider"
                        >

                            <Link
                                href="/provider/dashboard"
                                class="submenu-link"
                                @click="closeMobileMenu"
                            >

                                <i class="bi bi-briefcase"></i>

                                Dashboard Prestataire

                            </Link>

                        </li>

                        <li
                            v-if="isClient"
                        >

                            <Link
                                href="/dashboard"
                                class="submenu-link"
                                @click="closeMobileMenu"
                            >

                                <i class="bi bi-person-circle"></i>

                                Mon espace

                            </Link>

                        </li>

                        <li>

                            <Link
                                href="/profile"
                                class="submenu-link"
                                @click="closeMobileMenu"
                            >

                                <i class="bi bi-gear"></i>

                                Paramètres

                            </Link>

                        </li>

                    </ul>

                </li>

                <!-- GUEST -->

                <template v-if="!user">

                    <li>

                        <Link
                            href="/login"
                            class="nav-link"
                            @click="closeMobileMenu"
                        >

                            {{ $t('navigation.login') }}

                        </Link>

                    </li>

                    <li>

                        <Link
                            href="/register"
                            class="btn btn-primary"
                            @click="closeMobileMenu"
                        >

                            {{ $t('navigation.register') }}

                        </Link>

                    </li>

                </template>

                <!-- LOGOUT -->

                <li v-else>

                    <button
                        type="button"
                        class="btn btn-outline"
                        @click="logout"
                    >

                        <i class="bi bi-box-arrow-right"></i>

                        {{ $t('navigation.logout') }}

                    </button>

                </li>

                <!-- LANGUAGE -->

                <li class="lang-wrapper">

                    <select
                        class="lang-select"
                        :value="locale"
                        @change="changeLang($event.target.value)"
                    >

                        <option value="fr">
                            🇫🇷 Français
                        </option>

                        <option value="bm">
                            🇲🇱 Bambara
                        </option>

                        <option value="en">
                            🇬🇧 English
                        </option>

                    </select>

                </li>

                <!-- CTA -->

                <li class="navbar-cta">

                    <Link
                        href="/contact"
                        class="btn btn-primary"
                        @click="closeMobileMenu"
                    >

                        {{ $t('footer.contactUs') }}

                    </Link>

                </li>

            </ul>

        </nav>

        <!-- ================================================= -->
        <!-- BURGER -->
        <!-- ================================================= -->

        <button
            type="button"
            class="navbar-toggle"
            @click="menuOpen = !menuOpen"
        >

            <i
                :class="[
                    'bi',
                    menuOpen
                        ? 'bi-x-lg'
                        : 'bi-list'
                ]"
            ></i>

        </button>

    </div>

</template>
