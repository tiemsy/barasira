<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import ErrorState from '@/Components/ErrorState.vue'

const page = usePage()
const user = computed(() => page.props?.auth?.user ?? null)
const dashboardUrl = computed(() => {
    if (['admin', 'superadmin'].includes(user.value?.role)) return '/admin/dashboard'
    if (user.value?.role === 'prestataire') return '/provider/dashboard'
    if (user.value?.role === 'client') return '/dashboard'
    return '/'
})
</script>

<template>
    <AppLayout title="Accès refusé">
        <ErrorState
            code="403"
            eyebrow="Accès protégé"
            title="Vous n’avez pas accès à cette page."
            description="Cette section est réservée à un autre rôle ou nécessite des autorisations supplémentaires. Vérifiez votre compte ou revenez à votre espace."
            :primary-href="dashboardUrl"
            :primary-label="user ? 'Retour à mon espace' : 'Retour à l’accueil'"
        />
    </AppLayout>
</template>
