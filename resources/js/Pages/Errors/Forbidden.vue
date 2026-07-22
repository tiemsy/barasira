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
    <AppLayout :title="$t('ui.errors.forbiddenPage')">
        <ErrorState
            code="403"
            :eyebrow="$t('ui.errors.forbiddenEyebrow')"
            :title="$t('ui.errors.forbiddenTitle')"
            :description="$t('ui.errors.forbiddenDescription')"
            :primary-href="dashboardUrl"
            :primary-label="user ? $t('ui.errors.backDashboard') : $t('ui.errors.backHome')"
        />
    </AppLayout>
</template>
