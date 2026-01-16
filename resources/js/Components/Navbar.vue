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
const isAdmin = user?.role === 'admin'
const isClient = user?.role === 'client'
const isProvider = user?.role === 'prestataire'


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
            <ul>
                <li>
                    <Link href="/" class="nav-link">{{ $t('navigation.home') }}</Link>
                </li>
                <li>
                    <Link href="/services" class="nav-link">{{ $t('navigation.services') }}</Link>
                </li>
                <li>
                    <Link href="/missions" class="nav-link">{{ $t('navigation.missions') }}</Link>
                </li>

                <li>
                    <select @change="changeLang($event.target.value)">
                        <option value="fr">🇫🇷 {{ $t('lang.french') }}</option>
                        <option value="bm">🇲🇱 {{ $t('lang.bambara') }}</option>
                        <option value="en">🇬🇧 {{ $t('lang.english') }}</option>
                    </select>
                </li>

                <template v-if="user">
                    <li>
                        <Link v-if="isAdmin" href="/admin/dashboard" class="nav-link">{{ $t('navigation.profile') }}</Link>
                        <Link v-if="isProvider" href="/provider/dashboard" class="nav-link">{{ $t('navigation.profile') }}</Link>
                        <Link v-if="isClient" href="/dashboard" class="nav-link">{{ $t('navigation.profile') }}</Link>
                    </li>
                    <li>
                        <Link href="/logout" method="post" as="button" class="btn btn-outline">
                            {{ $t('navigation.logout') }}
                        </Link>
                    </li>
                </template>

                <template v-else>
                    <li>
                        <Link href="/login" class="btn btn-ghost">{{ $t('navigation.login') }}</Link>
                    </li>
                    <li>
                        <Link href="/register" class="btn btn-primary">{{ $t('navigation.register') }}</Link>
                    </li>
                </template>
            </ul>
        </nav>

        <!-- Burger -->
        <button class="navbar-toggle" @click="menuOpen = !menuOpen">
            ☰
        </button>

    </div>
</template>
