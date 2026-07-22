<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import ErrorState from '@/Components/ErrorState.vue'
</script>

<template>
    <AppLayout :title="$t('ui.errors.notFoundEyebrow')">
        <ErrorState
            code="404"
            :eyebrow="$t('ui.errors.notFoundEyebrow')"
            :title="$t('ui.errors.notFoundTitle')"
            :description="$t('ui.errors.notFoundDescription')"
        />
    </AppLayout>
</template>
