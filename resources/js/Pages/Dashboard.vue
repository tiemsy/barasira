<script>
    import Layout from './Layout.vue'

    export default {
        // Using a render function...
        layout: (h, page) => h(Layout, [page]),

        // Using shorthand syntax...
        layout: Layout,
    }
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4">
                <h1 class="text-2xl font-bold text-gray-900">
                    Tableau de bord
                </h1>
            </div>
        </header>

        <main class="max-w-7xl mx-auto py-6 px-4">
            <div class="bg-white p-6 rounded shadow">
                <p class="text-gray-700">
                    Bienvenue,
                    <strong></strong>
                </p>

                <p class="mt-2 text-sm text-gray-500">
                    Rôle :
                </p>

                <p class="mt-4">
                    Ceci est une page Inertia + Vue 3 correctement configurée.
                </p>
            </div>
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'


/**
 * Récupération des props Inertia
 * (envoyées depuis le controller Laravel)
 */
const page = usePage()

const users = computed(() => page.props.all.users)
</script>

<style scoped>
/* Styles optionnels */
</style>
