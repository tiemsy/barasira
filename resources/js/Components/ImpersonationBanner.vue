<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const impersonation = computed(() => page.props?.auth?.impersonation ?? null)
const user = computed(() => page.props?.auth?.user ?? null)
</script>

<template>
    <aside v-if="impersonation" class="impersonation-banner" role="status">
        <span><i class="fas fa-user-secret"></i>{{ $t('impersonation.active', { name: user?.name }) }}</span>
        <Link href="/impersonation/stop" method="post" as="button"><i class="fas fa-arrow-right-from-bracket"></i>{{ $t('impersonation.stop') }}</Link>
    </aside>
</template>

<style lang="scss" src="../../scss/components/_impersonation.scss"></style>
