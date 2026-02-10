<template>
    <AppLayout>
        <h1>{{ mission.title }}</h1>
    </AppLayout>
</template>

<script setup>
    import AppLayout from '@/Layouts/AppLayout.vue'
    defineProps({ mission: Object })
</script>
