<script setup>
import { computed, h } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'

const page = usePage()
const seo = computed(() => page.props.seo ?? {})
const structuredData = computed(() => JSON.stringify(seo.value.structured_data ?? {}).replace(/</g, '\\u003C'))
const StructuredData = () => h('script', {
    'head-key': 'structured-data',
    type: 'application/ld+json',
}, structuredData.value)
</script>

<template>
    <Head :title="seo.title">
        <meta head-key="description" name="description" :content="seo.description">
        <meta head-key="robots" name="robots" :content="seo.robots">
        <link head-key="canonical" rel="canonical" :href="seo.canonical">
        <meta head-key="og:type" property="og:type" :content="seo.type">
        <meta head-key="og:site_name" property="og:site_name" :content="seo.site_name">
        <meta head-key="og:title" property="og:title" :content="seo.title">
        <meta head-key="og:description" property="og:description" :content="seo.description">
        <meta head-key="og:url" property="og:url" :content="seo.canonical">
        <meta head-key="og:image" property="og:image" :content="seo.image">
        <meta head-key="twitter:card" name="twitter:card" content="summary_large_image">
        <meta head-key="twitter:title" name="twitter:title" :content="seo.title">
        <meta head-key="twitter:description" name="twitter:description" :content="seo.description">
        <meta head-key="twitter:image" name="twitter:image" :content="seo.image">
        <StructuredData />
    </Head>
</template>
