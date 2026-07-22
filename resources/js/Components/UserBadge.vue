<template>
    <div class="user-badge">
        <img :src="user.avatar_url || defaultAvatar" :alt="$t('ui.common.avatar')" class="w-10 h-10 rounded-full object-cover" />
        <div>
            <h4 class="font-semibold">{{ user.first_name }} {{ user.last_name }}</h4>
            <p class="text-gray-500 text-sm">{{ user.role }}</p>
            <span v-if="user.role === 'prestataire'" :class="['verified-badge', { 'is-unverified': !user.identity_verified_at }]" :title="user.identity_verified_at ? $t('serviceShow.verifiedProfile') : $t('serviceShow.unverifiedProfile', 'Profil non vérifié')">
                {{ user.identity_verified_at ? '✔️' : '⚠️' }} {{ user.identity_verified_at ? $t('serviceShow.verifiedProfile') : $t('serviceShow.unverifiedProfile', 'Profil non vérifié') }}
            </span>
        </div>
    </div>
</template>

<script setup>
defineProps({
    user: {
        type: Object,
        required: true,
        default: () => ({
            first_name: '',
            last_name: '',
            role: '',
            avatar_url: null
        })
    }
})

const defaultAvatar = '/images/default-avatar.png' // chemin vers ton avatar par défaut
</script>

<!-- A inclure dans le fichier cible : <UserBadge v-for="user in users" :key="user.id" :user="user" /> -->
