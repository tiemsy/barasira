<script setup>
defineProps({
    name: {
        type: String,
        required: true,
        validator: value => ['impersonate', 'edit', 'delete'].includes(value),
    },
})
</script>

<template>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <template v-if="name === 'impersonate'">
            <path d="M15 19.5a6.5 6.5 0 0 0-13 0" />
            <circle cx="8.5" cy="7.5" r="3.5" />
            <path d="M15 8h7m-3-3 3 3-3 3" />
        </template>
        <template v-else-if="name === 'edit'">
            <path d="M13.5 6.5 17.5 10.5" />
            <path d="m4 20 3.7-.8L19.4 7.5a2.1 2.1 0 0 0-3-3L4.8 16.2 4 20Z" />
        </template>
        <template v-else>
            <path d="M4 7h16M9 7V4h6v3m3 0-1 13H7L6 7m4 4v5m4-5v5" />
        </template>
    </svg>
</template>
