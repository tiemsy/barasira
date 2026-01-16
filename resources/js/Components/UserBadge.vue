<template>
    <div class="user-badge">
        <img :src="user.avatar_url || defaultAvatar" alt="Avatar" class="w-10 h-10 rounded-full object-cover" />
        <div>
            <h4 class="font-semibold">{{ user.first_name }} {{ user.last_name }}</h4>
            <p class="text-gray-500 text-sm">{{ user.role }}</p>
            <span v-if="user.email_verified_at" class="verified-badge" title="Compte vérifié">
                ✔️ Vérifié
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
