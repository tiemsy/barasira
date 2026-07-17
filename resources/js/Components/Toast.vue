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

<style scoped lang="scss" src="../../scss/components/_toast.scss"></style>
