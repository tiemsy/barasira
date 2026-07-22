<script setup>
defineProps({ name: { type: String, required: true } })
</script>

<template>
    <svg class="dashboard-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <template v-if="name === 'users'"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></template>
        <template v-else-if="name === 'provider'"><circle cx="9" cy="7" r="4"/><path d="M2 21v-2a4 4 0 0 1 4-4h6"/><circle cx="18" cy="18" r="3"/><path d="m18 14.5.6-1.2M18 22v-1M14.5 18H13m9 0h-1.5m-5-2.5-1-1m7 7-1-1"/></template>
        <template v-else-if="name === 'services' || name === 'tools'"><path d="M14.7 6.3a4 4 0 0 0-5-5L7 4l3 3 2.7-2.7a4 4 0 0 0 2 2Z"/><path d="m8.5 7.5-6.8 6.8a2.4 2.4 0 1 0 3.4 3.4l6.8-6.8M15 12l6 6-3 3-6-6"/></template>
        <template v-else-if="name === 'missions' || name === 'list'"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 5V3h8v2M8 11h8M8 15h6"/></template>
        <template v-else-if="name === 'active'"><path d="m13 2-9 12h8l-1 8 9-12h-8l1-8Z"/></template>
        <template v-else-if="name === 'completed'"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></template>
        <template v-else-if="name === 'messages'"><path d="M21 15a4 4 0 0 1-4 4H8l-5 3 1.5-5A7 7 0 0 1 3 13V8a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v7Z"/></template>
        <template v-else-if="name === 'mail'"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></template>
        <template v-else-if="name === 'rating'"><path d="m12 2.5 2.9 5.9 6.5.9-4.7 4.6 1.1 6.5-5.8-3.1-5.8 3.1 1.1-6.5-4.7-4.6 6.5-.9L12 2.5Z"/></template>
        <template v-else-if="name === 'applications'"><path d="m22 2-7 20-4-9-9-4 20-7Z"/><path d="M22 2 11 13"/></template>
        <template v-else-if="name === 'search'"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></template>
        <template v-else-if="name === 'profile'"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></template>
        <template v-else-if="name === 'shield'"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></template>
        <template v-else-if="name === 'plus'"><path d="M12 5v14M5 12h14"/></template>
        <template v-else-if="name === 'arrow'"><path d="M5 12h14m-5-5 5 5-5 5"/></template>
        <template v-else-if="name === 'chevron'"><path d="m9 18 6-6-6-6"/></template>
        <template v-else-if="name === 'store'"><path d="M3 10v11h18V10M5 10l1-7h12l1 7"/><path d="M8 21v-7h8v7M3 10a3 3 0 0 0 5 0 3 3 0 0 0 4 0 3 3 0 0 0 4 0 3 3 0 0 0 5 0"/></template>
        <template v-else-if="name === 'edit'"><path d="M13.5 6.5 17.5 10.5"/><path d="m4 20 3.7-.8L19.4 7.5a2.1 2.1 0 0 0-3-3L4.8 16.2 4 20Z"/></template>
        <template v-else-if="name === 'delete'"><path d="M4 7h16M9 7V4h6v3m3 0-1 13H7L6 7m4 4v5m4-5v5"/></template>
        <template v-else-if="name === 'filters'"><path d="M4 6h16M7 12h10M10 18h4"/><circle cx="8" cy="6" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="12" cy="18" r="1"/></template>
        <template v-else-if="name === 'location'"><path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></template>
        <template v-else-if="name === 'wallet'"><path d="M4 6h14a2 2 0 0 1 2 2v10H4a2 2 0 0 1-2-2V6a3 3 0 0 1 3-3h12"/><path d="M16 11h5v4h-5a2 2 0 0 1 0-4Z"/></template>
        <template v-else-if="name === 'coins'"><circle cx="9" cy="8" r="5"/><path d="M6 8h6M9 5v6M14 7a5 5 0 1 1-2 9.6"/></template>
        <template v-else-if="name === 'calendar'"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4m10-4v4M3 10h18m-13 4 2 2 5-5"/></template>
        <template v-else-if="name === 'education'"><path d="m2 9 10-5 10 5-10 5L2 9Z"/><path d="M6 11.5V16c3 3 9 3 12 0v-4.5M22 9v6"/></template>
        <template v-else-if="name === 'experience'"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V4h6v3m-12 5c5 3 13 3 18 0m-9-1v4"/></template>
        <template v-else-if="name === 'certificate'"><circle cx="12" cy="9" r="6"/><path d="m8.5 14-1 8 4.5-2 4.5 2-1-8M9.5 9l1.5 1.5L14.5 7"/></template>
        <template v-else-if="name === 'verified'"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></template>
        <template v-else-if="name === 'external'"><path d="M14 4h6v6m0-6-9 9"/><path d="M18 13v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h6"/></template>
        <template v-else-if="name === 'login'"><path d="M10 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h5m5-4 4-4-4-4m4 4H9"/></template>
        <template v-else-if="name === 'arrow-up'"><path d="m5 11 7-7 7 7M12 4v16"/></template>
        <template v-else-if="name === 'arrow-left'"><path d="m11 5-7 7 7 7M4 12h16"/></template>
        <template v-else-if="name === 'logout'"><path d="M10 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h5m5-4 4-4-4-4m4 4H9"/></template>
        <template v-else-if="name === 'impersonation'"><circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0m3-14v6m-3-3h6"/></template>
        <template v-else-if="name === 'terminal'"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="m7 9 3 3-3 3m5 0h5"/></template>
        <template v-else-if="name === 'lock'"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 4v3"/></template>
        <template v-else-if="name === 'google'"><circle cx="12" cy="12" r="9"/><path d="M20 12h-8m6.5-5.5A8 8 0 1 0 20 12"/></template>
        <template v-else-if="name === 'facebook'"><circle cx="12" cy="12" r="9"/><path d="M14 8h-2a2 2 0 0 0-2 2v11m-3-8h7"/></template>
        <template v-else-if="name === 'headset'"><path d="M4 14v-2a8 8 0 0 1 16 0v2"/><path d="M4 14h3v6H5a1 1 0 0 1-1-1v-5Zm16 0h-3v6h2a1 1 0 0 0 1-1v-5ZM17 20c-1 2-3 2-5 2"/></template>
        <template v-else-if="name === 'phone'"><path d="M5 3h4l2 5-3 2a16 16 0 0 0 6 6l2-3 5 2v4a2 2 0 0 1-2 2C10 20 4 14 3 5a2 2 0 0 1 2-2Z"/></template>
        <template v-else-if="name === 'clock'"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></template>
        <template v-else-if="name === 'lightbulb'"><path d="M9 18h6m-5 3h4M8 14a7 7 0 1 1 8 0c-1 1-1 2-1 4H9c0-2 0-3-1-4Z"/></template>
        <template v-else-if="name === 'alert' || name === 'warning'"><path d="M12 3 2.5 20h19L12 3Z"/><path d="M12 9v5m0 3h.01"/></template>
        <template v-else-if="name === 'chevron-down'"><path d="m6 9 6 6 6-6"/></template>
        <template v-else-if="name === 'tag'"><path d="M20 13 13 20l-9-9V4h7l9 9Z"/><circle cx="8" cy="8" r="1"/></template>
        <template v-else-if="name === 'check'"><path d="m5 12 4 4L19 6"/></template>
        <template v-else-if="name === 'send'"><path d="m22 2-7 20-4-9-9-4 20-7Z"/><path d="M22 2 11 13"/></template>
        <template v-else-if="name === 'camera'"><path d="M4 8h3l2-3h6l2 3h3a2 2 0 0 1 2 2v9H2v-9a2 2 0 0 1 2-2Z"/><circle cx="12" cy="13" r="4"/></template>
        <template v-else-if="name === 'server' || name === 'storage'"><rect x="3" y="4" width="18" height="6" rx="2"/><rect x="3" y="14" width="18" height="6" rx="2"/><path d="M7 7h.01M7 17h.01"/></template>
        <template v-else-if="name === 'status'"><path d="M4 17V7m5 10v-5m5 5V4m5 13V9"/></template>
        <template v-else-if="name === 'refresh'"><path d="M20 7V3l-2 2a8 8 0 1 0 2 9M20 3h-4"/></template>
        <template v-else-if="name === 'ordered-list'"><path d="M10 6h11M10 12h11M10 18h11M4 5h1v3M4 11h2l-2 3h2M4 17h2v3H4"/></template>
        <template v-else-if="name === 'disconnected'"><path d="M8 12h8m-4-4v8M5 5l14 14"/><circle cx="12" cy="12" r="9"/></template>
        <template v-else-if="name === 'file-check'"><path d="M6 2h8l4 4v16H6V2Z"/><path d="M14 2v5h5m-10 8 2 2 4-4"/></template>
        <template v-else-if="name === 'user-plus'"><circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0m3-8v6m-3-3h6"/></template>
        <template v-else-if="name === 'plumbing'"><path d="M4 7h10v4H4zM7 3v4m4-4v4m3 6v2a5 5 0 0 1-10 0v-4"/><path d="M18 8c0 2-2 3-2 5a2 2 0 1 0 4 0c0-2-2-3-2-5Z"/></template>
        <template v-else-if="name === 'electrical'"><path d="m13 2-9 12h8l-1 8 9-12h-8l1-8Z"/></template>
        <template v-else-if="name === 'tailoring'"><circle cx="6" cy="7" r="3"/><circle cx="6" cy="17" r="3"/><path d="m8.5 8.5 12 8.5M8.5 15.5 20.5 7"/></template>
        <template v-else-if="name === 'transport'"><path d="M3 6h12v11H3zM15 10h4l2 3v4h-6z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/></template>
        <template v-else-if="name === 'gardening' || name === 'agriculture'"><path d="M12 21V9m0 5c-5 0-8-3-8-8 5 0 8 3 8 8Zm0-4c5 0 8-3 8-8-5 0-8 3-8 8Z"/></template>
        <template v-else-if="name === 'cleaning'"><path d="m9 3 6 2-5 16H4L9 3Zm2 9h6m-7 4h8m-9 4h10"/></template>
        <template v-else-if="name === 'computer'"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8m-4-4v4"/></template>
        <template v-else-if="name === 'building' || name === 'construction'"><path d="M4 21V5h10v16M14 10h6v11M8 9h2m-2 4h2m-2 4h2m9-3h-2m2 4h-2"/></template>
        <template v-else-if="name === 'beauty'"><path d="M12 21c5-3 7-7 7-11-4 0-7 2-7 6 0-4-3-6-7-6 0 4 2 8 7 11Z"/><path d="M12 16V5"/></template>
        <template v-else-if="name === 'food'"><path d="M7 3v8m-3-8v5a3 3 0 0 0 6 0V3M7 11v10m10-18v18m0-18c-4 3-4 8 0 10"/></template>
        <template v-else-if="name === 'mechanic'"><circle cx="12" cy="12" r="3"/><path d="m19 13 2 1-2 4-2-1-2 2v2h-6v-2l-2-2-2 1-2-4 2-1v-2L3 10l2-4 2 1 2-2V3h6v2l2 2 2-1 2 4-2 1v2Z"/></template>
        <template v-else-if="name === 'solar'"><circle cx="12" cy="7" r="3"/><path d="M12 1v2m0 8v2M6 7H4m16 0h-2M7.8 2.8 6.4 1.4m9.8 1.4 1.4-1.4M4 15h16l-2 7H6l-2-7Z"/></template>
        <template v-else-if="name === 'cooling'"><path d="M12 2v20M4 7l16 10M4 17 20 7M9 4l3 3 3-3M9 20l3-3 3 3"/></template>
        <template v-else-if="name === 'info'"><circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-10h.01"/></template>
        <template v-else-if="name === 'identity'"><circle cx="9" cy="9" r="3"/><path d="M4 19a5 5 0 0 1 10 0M15 7h5m-5 4h5m-5 4h3"/></template>
        <template v-else-if="name === 'shield-lock'"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><rect x="9" y="10" width="6" height="5" rx="1"/><path d="M10.5 10V8.5a1.5 1.5 0 0 1 3 0V10"/></template>
        <template v-else-if="name === 'eye'"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></template>
        <template v-else-if="name === 'eye-off'"><path d="M3 3l18 18M10.5 5.2A10 10 0 0 1 12 5c6 0 10 7 10 7a18 18 0 0 1-3 3.8M6.2 6.2C3.5 8.1 2 12 2 12s4 7 10 7c1.5 0 2.8-.4 4-1"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></template>
        <template v-else-if="name === 'code'"><path d="m8 9-4 3 4 3m8-6 4 3-4 3m-2-9-4 12"/></template>
        <template v-else-if="name === 'framework'"><path d="m12 2 9 5v10l-9 5-9-5V7l9-5Z"/><path d="m3 7 9 5 9-5m-9 5v10"/></template>
        <template v-else-if="name === 'loading'"><path d="M21 12a9 9 0 1 1-3-6.7"/></template>
        <template v-else><circle cx="12" cy="12" r="9"/><path d="M12 8v4m0 4h.01"/></template>
    </svg>
</template>

<style scoped>
.dashboard-icon { display: inline-block; width: 1.15em; height: 1.15em; flex: 0 0 auto; vertical-align: -.15em; }
</style>
