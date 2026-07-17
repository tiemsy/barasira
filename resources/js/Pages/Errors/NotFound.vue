<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import ErrorState from '@/Components/ErrorState.vue'
</script>

<template>
    <AppLayout title="Page introuvable">
        <ErrorState
            code="404"
            eyebrow="Page introuvable"
            title="Cette page semble avoir disparu."
            description="L’adresse est peut-être incorrecte ou la page a été déplacée. Vous pouvez revenir à l’accueil ou consulter la page précédente."
        />
    </AppLayout>
</template>
