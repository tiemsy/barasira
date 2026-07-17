<script setup>
import { computed, onBeforeUnmount, reactive, ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import profileService from '@/composables/profileService'

const props = defineProps({
    profile: { type: Object, required: true },
})

const form = reactive({
    first_name: props.profile.first_name ?? '',
    last_name: props.profile.last_name ?? '',
    email: props.profile.email ?? '',
    phone: props.profile.phone ?? '',
    bio: props.profile.bio ?? '',
})
const avatar = ref(null)
const avatarInput = ref(null)
const avatarPreview = ref(props.profile.avatar_url ?? '')
const removeAvatar = ref(false)
let objectUrl = null
const errors = ref({})
const saving = ref(false)
const message = ref('')
const initials = computed(() => `${form.first_name?.[0] ?? ''}${form.last_name?.[0] ?? ''}`.toUpperCase())

function releaseObjectUrl() {
    if (objectUrl) URL.revokeObjectURL(objectUrl)
    objectUrl = null
}

function selectAvatar(event) {
    const file = event.target.files?.[0]
    errors.value.avatar = null
    if (!file) return

    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 3 * 1024 * 1024) {
        errors.value.avatar = [file.size > 3 * 1024 * 1024
            ? event.target.dataset.sizeError
            : event.target.dataset.typeError]
        event.target.value = ''
        return
    }

    releaseObjectUrl()
    avatar.value = file
    objectUrl = URL.createObjectURL(file)
    avatarPreview.value = objectUrl
    removeAvatar.value = false
}

function clearAvatar() {
    releaseObjectUrl()
    avatar.value = null
    avatarPreview.value = ''
    removeAvatar.value = true
    if (avatarInput.value) avatarInput.value.value = ''
}

onBeforeUnmount(releaseObjectUrl)

async function submit() {
    if (saving.value) return
    saving.value = true
    errors.value = {}
    message.value = ''

    try {
        const { data } = await profileService.update(props.profile.id, {
            ...form,
            avatar: avatar.value,
            remove_avatar: removeAvatar.value ? 1 : 0,
        })
        if (data.requires_email_verification) {
            router.visit('/email/verify')
            return
        }
        message.value = data.message
        window.setTimeout(() => router.visit('/profile'), 700)
    } catch (error) {
        errors.value = error.response?.data?.errors ?? {}
        message.value = error.response?.data?.message ?? ''
    } finally {
        saving.value = false
    }
}
</script>

<template>
    <AppLayout :title="$t('profile.edit')">
        <main class="profile-page profile-edit-page">
            <header class="profile-form-header">
                <div>
                    <span class="profile-eyebrow">{{ $t('profile.account') }}</span>
                    <h1>{{ $t('profile.edit') }}</h1>
                    <p>{{ $t('profile.editHint') }}</p>
                </div>
                <Link href="/profile" class="profile-cancel">{{ $t('profile.cancel') }}</Link>
            </header>

            <form class="profile-form" @submit.prevent="submit">
                <aside class="profile-photo-card">
                    <div class="profile-avatar" :class="{ 'has-image': avatarPreview }">
                        <img v-if="avatarPreview" :src="avatarPreview" alt="">
                        <span v-else>{{ initials }}</span>
                    </div>
                    <label class="profile-upload-button" for="avatar">
                        <i class="fas fa-camera" aria-hidden="true"></i>
                        {{ avatarPreview ? $t('profile.changePhoto') : $t('profile.addPhoto') }}
                    </label>
                    <input
                        id="avatar"
                        ref="avatarInput"
                        class="profile-file-input"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        :data-size-error="$t('profile.photoTooLarge')"
                        :data-type-error="$t('profile.photoInvalidType')"
                        @change="selectAvatar"
                    >
                    <button v-if="avatarPreview" type="button" class="profile-remove-photo" @click="clearAvatar">
                        {{ $t('profile.removePhoto') }}
                    </button>
                    <p class="profile-photo-hint">{{ $t('profile.photoHint') }}</p>
                    <p v-if="errors.avatar" class="field-error">{{ errors.avatar[0] }}</p>
                </aside>

                <section class="profile-fields-card">
                    <div class="profile-field-grid">
                        <label>
                            <span>{{ $t('profile.firstName') }}</span>
                            <input v-model.trim="form.first_name" type="text" maxlength="100" required autocomplete="given-name">
                            <small v-if="errors.first_name" class="field-error">{{ errors.first_name[0] }}</small>
                        </label>
                        <label>
                            <span>{{ $t('profile.lastName') }}</span>
                            <input v-model.trim="form.last_name" type="text" maxlength="100" required autocomplete="family-name">
                            <small v-if="errors.last_name" class="field-error">{{ errors.last_name[0] }}</small>
                        </label>
                        <label>
                            <span>{{ $t('profile.email') }}</span>
                            <input v-model.trim="form.email" type="email" maxlength="150" required autocomplete="email">
                            <small v-if="errors.email" class="field-error">{{ errors.email[0] }}</small>
                            <small>{{ $t('profile.emailHint') }}</small>
                        </label>
                        <label>
                            <span>{{ $t('profile.phone') }}</span>
                            <input v-model.trim="form.phone" type="tel" maxlength="30" autocomplete="tel">
                            <small v-if="errors.phone" class="field-error">{{ errors.phone[0] }}</small>
                        </label>
                        <label class="full-width">
                            <span>{{ $t('profile.bio') }}</span>
                            <textarea v-model.trim="form.bio" rows="6" maxlength="2000" :placeholder="$t('profile.bioPlaceholder')"></textarea>
                            <small v-if="errors.bio" class="field-error">{{ errors.bio[0] }}</small>
                            <small class="character-count">{{ form.bio.length }}/2000</small>
                        </label>
                    </div>

                    <p v-if="message" class="profile-form-message" role="status">{{ message }}</p>
                    <footer>
                        <Link href="/profile" class="profile-cancel">{{ $t('profile.cancel') }}</Link>
                        <button type="submit" :disabled="saving">
                            {{ saving ? $t('profile.saving') : $t('profile.save') }}
                        </button>
                    </footer>
                </section>
            </form>
        </main>
    </AppLayout>
</template>

<style scoped lang="scss" src="../../../scss/pages/_profile.scss"></style>
