<script setup>
import { useToastStore } from '@/stores/toast'
const toast = useToastStore()
</script>

<template>
    <transition name="fade">
        <div v-if="toast.message" class="toast" :class="toast.type">
            {{ toast.message }}
        </div>
    </transition>
</template>

<style scoped>
.toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    padding: 14px 22px;
    border-radius: 10px;
    color: white;
}

.success {
    background: #198754;
}

.error {
    background: #dc3545;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity .3s;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
