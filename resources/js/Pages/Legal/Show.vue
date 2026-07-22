<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({ document: { type: Object, required: true }, documentKey: { type: String, required: true }, updatedAt: String, contactEmail: String })
const documents = [
    ['cgu', '/legal/cgu'], ['cgv', '/legal/cgv'], ['confidentialite', '/legal/confidentialite'],
    ['cookies', '/legal/cookies'], ['moderation', '/legal/moderation'], ['kyc', '/legal/kyc'],
]
</script>

<template>
    <AppLayout><main class="legal-page"><header class="legal-hero"><span>{{ $t('legal.eyebrow') }}</span><h1>{{ document.title }}</h1><p>{{ document.intro }}</p><small>{{ $t('legal.updated', { date: updatedAt }) }}</small></header><div class="legal-layout"><aside><strong>{{ $t('legal.documents') }}</strong><nav><Link v-for="item in documents" :key="item[0]" :href="item[1]" :class="{ active: documentKey === item[0] }">{{ $t(`legal.links.${item[0]}`) }}</Link></nav></aside><article><section v-for="section in document.sections" :key="section.title"><h2>{{ section.title }}</h2><p v-for="paragraph in section.paragraphs" :key="paragraph">{{ paragraph }}</p></section><div class="legal-contact"><strong>{{ $t('legal.question') }}</strong><a :href="`mailto:${contactEmail}`">{{ contactEmail }}</a></div></article></div></main></AppLayout>
</template>

<style scoped>
.legal-page{min-height:70vh;background:#f7faf8;color:#17251e;padding-bottom:5rem}.legal-hero{padding:4.5rem 1rem;background:linear-gradient(125deg,#0c3d27,#177245);color:#fff;text-align:center}.legal-hero>span{color:#f4c968;font-size:.75rem;font-weight:900;letter-spacing:.12em;text-transform:uppercase}.legal-hero h1{margin:.6rem 0;font-size:clamp(2rem,5vw,3.6rem)}.legal-hero p{max-width:760px;margin:0 auto 1rem;color:rgba(255,255,255,.82);font-size:1.08rem;line-height:1.7}.legal-hero small{opacity:.7}.legal-layout{display:grid;grid-template-columns:260px minmax(0,1fr);gap:2rem;width:min(1100px,calc(100% - 2rem));margin:3rem auto 0;align-items:start}.legal-layout aside{position:sticky;top:1rem;padding:1.25rem;border:1px solid #dfe8e2;border-radius:16px;background:#fff}.legal-layout aside>strong{display:block;margin-bottom:.8rem;color:#66756c;font-size:.72rem;letter-spacing:.08em;text-transform:uppercase}.legal-layout nav{display:grid;gap:.35rem}.legal-layout nav a{padding:.65rem .75rem;border-radius:8px;color:#405047;text-decoration:none}.legal-layout nav a:hover,.legal-layout nav a.active{background:#e7f4ec;color:#176b45;font-weight:800}.legal-layout article{padding:2.2rem;border:1px solid #dfe8e2;border-radius:18px;background:#fff;box-shadow:0 12px 35px rgba(23,72,45,.06)}.legal-layout section+section{margin-top:2rem;padding-top:1.5rem;border-top:1px solid #e5ece7}.legal-layout h2{margin:0 0 .8rem;color:#124d32;font-size:1.25rem}.legal-layout p{color:#4f5f56;line-height:1.8}.legal-contact{display:flex;gap:.7rem;margin-top:2rem;padding:1rem;border-radius:10px;background:#edf7f1}.legal-contact a{color:#176b45;font-weight:800}@media(max-width:760px){.legal-layout{grid-template-columns:1fr}.legal-layout aside{position:static}.legal-layout article{padding:1.3rem}}
</style>
