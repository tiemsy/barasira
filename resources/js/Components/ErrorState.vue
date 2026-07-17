<script setup>
import { Link, router } from '@inertiajs/vue3'

defineProps({
    code: { type: String, required: true },
    eyebrow: { type: String, required: true },
    title: { type: String, required: true },
    description: { type: String, required: true },
    primaryHref: { type: String, default: '/' },
    primaryLabel: { type: String, default: 'Retour à l’accueil' },
    secondaryLabel: { type: String, default: 'Page précédente' },
})

function goBack() {
    if (window.history.length > 1) {
        window.history.back()
        return
    }

    router.visit('/')
}
</script>

<template>
    <section class="error-state">
        <div class="error-state__ambient error-state__ambient--one" />
        <div class="error-state__ambient error-state__ambient--two" />

        <div class="error-state__card">
            <div class="error-state__visual" aria-hidden="true">
                <span class="error-state__code">{{ code }}</span>
                <div class="error-state__shield">
                    <span>!</span>
                </div>
            </div>

            <div class="error-state__content">
                <span class="error-state__eyebrow">{{ eyebrow }}</span>
                <h1>{{ title }}</h1>
                <p>{{ description }}</p>

                <div class="error-state__actions">
                    <Link :href="primaryHref" class="error-state__primary">
                        {{ primaryLabel }}
                        <span aria-hidden="true">→</span>
                    </Link>
                    <button type="button" class="error-state__secondary" @click="goBack">
                        ← {{ secondaryLabel }}
                    </button>
                </div>

                <p class="error-state__help">
                    Vous pensez qu’il s’agit d’une erreur ?
                    <Link href="/contact-us">Contactez notre équipe.</Link>
                </p>
            </div>
        </div>
    </section>
</template>
