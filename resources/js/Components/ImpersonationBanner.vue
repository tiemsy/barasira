<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const page = usePage()
const impersonation = computed(() => page.props?.auth?.impersonation ?? null)
const user = computed(() => page.props?.auth?.user ?? null)
const isImpersonating = computed(() => Boolean(
    user.value
    && impersonation.value?.active === true
    && impersonation.value?.role === 'superadmin'
    && Number(impersonation.value?.target_id) === Number(user.value.id)
    && Number(impersonation.value?.id) !== Number(user.value.id)
))
</script>

<template>
    <aside v-if="isImpersonating" class="impersonation-banner" role="status">
        <span><DashboardIcon name="impersonation" />{{ $t('impersonation.active', { name: user?.name }) }}</span>
        <Link href="/impersonation/stop" method="post" as="button"><DashboardIcon name="logout" />{{ $t('impersonation.stop') }}</Link>
    </aside>
</template>

<style lang="scss" src="../../scss/components/_impersonation.scss"></style>
