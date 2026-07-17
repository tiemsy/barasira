<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    user: { type: Object, default: null },
    canManageSuperAdmin: { type: Boolean, default: false },
})
const editing = computed(() => Boolean(props.user?.id))
const form = useForm({
    first_name: props.user?.first_name ?? '',
    last_name: props.user?.last_name ?? '',
    email: props.user?.email ?? '',
    phone: props.user?.phone ?? '',
    role: props.user?.role ?? 'client',
    bio: props.user?.bio ?? '',
    verified: props.user?.verified ?? false,
    password: '',
    password_confirmation: '',
})

function submit() {
    if (editing.value) form.put(`/admin/users/${props.user.id}`)
    else form.post('/admin/users')
}
</script>

<template>
    <Head :title="editing ? $t('adminUsers.editTitle') : $t('adminUsers.createTitle')" />
    <AppLayout>
        <main class="admin-user-form-page">
            <header class="admin-user-form-header">
                <div><span>{{ $t('adminUsers.eyebrow') }}</span><h1>{{ editing ? $t('adminUsers.editTitle') : $t('adminUsers.createTitle') }}</h1><p>{{ editing ? $t('adminUsers.editSubtitle') : $t('adminUsers.createSubtitle') }}</p></div>
                <Link href="/admin/users">{{ $t('adminUsers.back') }}</Link>
            </header>

            <form class="admin-user-form-card" @submit.prevent="submit">
                <section>
                    <header><h2>{{ $t('adminUsers.identity') }}</h2><p>{{ $t('adminUsers.identityHint') }}</p></header>
                    <div class="admin-user-form-grid">
                        <label><span>{{ $t('profile.firstName') }}</span><input v-model.trim="form.first_name" required maxlength="100"><small v-if="form.errors.first_name">{{ form.errors.first_name }}</small></label>
                        <label><span>{{ $t('profile.lastName') }}</span><input v-model.trim="form.last_name" required maxlength="100"><small v-if="form.errors.last_name">{{ form.errors.last_name }}</small></label>
                        <label><span>{{ $t('profile.email') }}</span><input v-model.trim="form.email" type="email" required maxlength="150"><small v-if="form.errors.email">{{ form.errors.email }}</small></label>
                        <label><span>{{ $t('profile.phone') }}</span><input v-model.trim="form.phone" type="tel" maxlength="30"><small v-if="form.errors.phone">{{ form.errors.phone }}</small></label>
                        <label><span>{{ $t('adminUsers.role') }}</span><select v-model="form.role"><option value="client">{{ $t('navbar.roles.client') }}</option><option value="prestataire">{{ $t('navbar.roles.provider') }}</option><option value="admin">{{ $t('navbar.roles.admin') }}</option><option v-if="canManageSuperAdmin" value="superadmin">{{ $t('navbar.roles.superadmin') }}</option></select><small v-if="form.errors.role">{{ form.errors.role }}</small></label>
                        <label class="admin-user-check"><input v-model="form.verified" type="checkbox"><span>{{ $t('adminUsers.markVerified') }}</span></label>
                        <label class="full"><span>{{ $t('profile.bio') }}</span><textarea v-model.trim="form.bio" rows="5" maxlength="2000"></textarea><small v-if="form.errors.bio">{{ form.errors.bio }}</small></label>
                    </div>
                </section>

                <section>
                    <header><h2>{{ $t('adminUsers.security') }}</h2><p>{{ editing ? $t('adminUsers.passwordOptional') : $t('adminUsers.passwordRequired') }}</p></header>
                    <div class="admin-user-form-grid">
                        <label><span>{{ $t('adminUsers.password') }}</span><input v-model="form.password" type="password" :required="!editing" minlength="8" autocomplete="new-password"><small v-if="form.errors.password">{{ form.errors.password }}</small></label>
                        <label><span>{{ $t('adminUsers.passwordConfirmation') }}</span><input v-model="form.password_confirmation" type="password" :required="!editing" minlength="8" autocomplete="new-password"></label>
                    </div>
                </section>

                <footer><Link href="/admin/users">{{ $t('profile.cancel') }}</Link><button type="submit" :disabled="form.processing">{{ form.processing ? $t('profile.saving') : $t('profile.save') }}</button></footer>
            </form>
        </main>
    </AppLayout>
</template>

<style lang="scss" src="../../../../scss/pages/admin/_users.scss"></style>
