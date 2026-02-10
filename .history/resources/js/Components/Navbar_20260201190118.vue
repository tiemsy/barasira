<script setup>
import { onMounted, ref } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import { api } from '@/lib/api'
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()
const changeLang = (lang) => {
    locale.value = lang
    localStorage.setItem('lang', lang)
}

const menuOpen = ref(false)
const page = usePage()

const user = page.props[1].auth?.user || null
// const user = usePage().props.auth?.user || null
const isAdmin = user?.role === 'admin'
const isClient = user?.role === 'client'
const isProvider = user?.role === 'prestataire'

// start
const activeSubmenu = ref(null)

const toggleSubmenu = (name) => {
  activeSubmenu.value = activeSubmenu.value === name ? null : name
}
// end


onMounted(async () => {
    try {
        const res = await api.get('/me')
        user.value = res.data
    } catch (error) {
        // Cas normal : utilisateur non connecté
        // user.value = null
        // console.log(error);

    } finally {
        // loading.value = false
    }
})

const logout = () => {
    router.post(route('logout'))
}

</script>

<template>
    <div class="container navbar-container">
        <!-- Logo -->
        <Link href="/" class="navbar-logo">
            <img src="../../../public/images/logo-barasira.png" alt="Barasira" />
            <span>Barasira</span>
        </Link>

        <!-- Menu -->
        <nav :class="['navbar-links', { open: menuOpen }]">
            <ul class="nav-list">

            <!-- LANG -->
                <li>
                    <select class="lang-select" @change="changeLang($event.target.value)">
                        <option value="fr">🇫🇷 Français</option>
                        <option value="bm">🇲🇱 Bambara</option>
                        <option value="en">🇬🇧 English</option>
                    </select>
                </li>

                <!-- HOME -->
                <li>
                    <Link href="/" class="nav-link">
                        {{ $t('navigation.home') }}
                    </Link>
                </li>

                <!-- SERVICES -->
                <li>
                    <Link href="/services" class="nav-link">
                        {{ $t('navigation.services') }}
                    </Link>
                </li>

                <!-- AUTHENTICATED -->
                <template v-if="user">

                    <!-- MISSIONS (CLIENT) -->
                    <li v-if="isClient" class="nav-item has-submenu" @click="toggleSubmenu('missions')">
                        <span class="nav-link">
                            {{ $t('navigation.missions') }}
                            <span class="chevron">▾</span>
                        </span>

                        <ul class="submenu" :class="{ open: activeSubmenu === 'missions' }">
                            <li>
                                <Link href="/missions/index" class="submenu-link">
                                    📋 Mes missions
                                </Link>
                            </li>
                            <li>
                                <Link href="/missions/create" class="submenu-link">
                                    ➕ Créer une mission
                                </Link>
                            </li>
                        </ul>
                    </li>

                    <!-- PROFIL -->
                    <li class="nav-item has-submenu" @click="toggleSubmenu('profile')">
                        <span class="nav-link">
                            {{ $t('navigation.profile') }}
                            <span class="chevron">▾</span>
                        </span>

                        <ul class="submenu" :class="{ open: activeSubmenu === 'profile' }">
                            <li v-if="isAdmin">
                                <Link href="/admin/dashboard" class="submenu-link">
                                    🛠 Admin
                                </Link>
                            </li>
                            <li v-if="isProvider">
                                <Link href="/provider/dashboard" class="submenu-link">
                                    👷 Prestataire
                                </Link>
                            </li>
                            <li v-if="isClient">
                                <Link href="/dashboard" class="submenu-link">
                                    👤 Client
                                </Link>
                            </li>
                        </ul>
                    </li>

                    <!-- LOGOUT -->
                    <li>
                        <Link href="/logout" method="post" as="button" class="btn btn-outline">
                            {{ $t('navigation.logout') }}
                        </Link>
                    </li>
                </template>

                <!-- GUEST -->
                <template v-else>
                    <li>
                        <Link href="/login" class="nav-link">
                            {{ $t('navigation.login') }}
                        </Link>
                    </li>
                    <li>
                        <Link href="/register" class="nav-link">
                            {{ $t('navigation.register') }}
                        </Link>
                    </li>
                </template>

                <li>
                    <button class="btn btn-primary">{{ $t('footer.contactUs') }}</button>
                </li>

            </ul>
        </nav>


        <!-- Burger -->
        <button class="navbar-toggle" @click="menuOpen = !menuOpen">
            ☰
        </button>

    </div>
</template>
