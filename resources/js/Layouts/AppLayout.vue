<template>
    <ImpersonationBanner />
    <!-- Navbar -->
    <header class="navbar">
        <Navbar />
    </header>

    <!-- Contenu principal -->
    <main>
        <slot />
    </main>

    <Toast />
    <ConfirmDialog />

    <!-- Footer -->
    <Footer />
</template>

<script setup>
import { defineProps } from 'vue'
// import { Link, usePage } from '@inertiajs/vue3'
import Navbar from '@/Components/Navbar.vue'
import Footer from '@/Components/Footer.vue'
import Toast from '@/Components/Toast.vue'
import ImpersonationBanner from '@/Components/ImpersonationBanner.vue'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'

/**
 * Utilisateur authentifié (si présent)
 * Doit être partagé via Inertia::share()
 */
const props = defineProps({
    title: String
})
// const page = usePage()
// const user = computed(() => page.props.auth?.user ?? null)
</script>
