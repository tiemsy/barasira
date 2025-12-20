<script setup>
    import { Link } from '@inertiajs/vue3'
</script>

<template>
    <main>
        <header>
            <Link href="/">Accueil</Link>
            <Link href="/about">A propos de nous</Link>
            <Link href="/contact">Nous contacter</Link>
        </header>
        <article>
            <slot />
        </article>
    </main>
</template>
