<template>
    <div class="space-y-4 max-w-sm mx-auto">
        <h2 class="text-xl font-semibold mb-2">{{ $t('ui.payments.chooseMethod') }}</h2>

        <button @click="select('Orange Money')"
            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded transition">
            Orange Money
        </button>

        <button @click="select('Moov Money')"
            class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition">
            Moov Money
        </button>

        <button @click="select('Wave')"
            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">
            Wave
        </button>
    </div>
</template>

<script setup>
import { defineEmits } from 'vue'

const emits = defineEmits(['select'])

function select(method) {
    emits('select', method)
}
</script>
