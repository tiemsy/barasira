<script setup>
    import { Link } from '@inertiajs/vue3'
</script>

<template>
    <main>
        <header>
            <Link href="/">{{ $t('navigation.home') }}</Link>
            <Link href="/services">{{ $t('navigation.services') }}</Link>
            <Link href="/missions">{{ $t('navigation.missions') }}</Link>
            <Link href="/register">{{ $t('home.heroBtnBecomeProvider') }}</Link>
            <Link href="/login">{{ $t('navigation.login') }}</Link>
        </header>
        <article>
            <slot />
        </article>
    </main>
</template>
