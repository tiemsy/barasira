<script setup>
    import { Link } from '@inertiajs/vue3'
</script>

<template>
    <main>
        <header>
            <Link href="/">Accueil</Link>
            <Link href="/services">Service</Link>
            <Link href="/missions">Missions</Link>
            <Link href="/">Devenir prestataire</Link>
            <Link href="/login">Connexion</Link>
        </header>
        <article>
            <slot />
        </article>
    </main>
</template>
