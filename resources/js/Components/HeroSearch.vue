<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

defineProps({
    categories: { type: Array, default: () => [] },
    cities: { type: Array, default: () => [] },
})

const filters = reactive({
    keyword: '',
    category: '',
    city: '',
})

function search() {
    const query = Object.fromEntries(
        Object.entries(filters).filter(([, value]) => value !== '')
    )

    router.visit('/services', {
        data: query,
        preserveState: true,
    })
}
</script>

<template>
    <form class="hero-search" :aria-label="$t('home.search.formLabel')" @submit.prevent="search">
        <div class="hero-search__main">
            <span class="hero-search__icon" aria-hidden="true">⌕</span>
            <input
                v-model.trim="filters.keyword"
                type="search"
                :placeholder="$t('home.search.placeholder')"
                :aria-label="$t('home.search.inputLabel')"
            >
            <button type="submit">{{ $t('home.search.submit') }}</button>
        </div>

        <div class="hero-filters">
            <label>
                <span>{{ $t('home.search.category') }}</span>
                <select v-model="filters.category">
                    <option value="">{{ $t('home.search.allCategories') }}</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
            </label>
            <label>
                <span>{{ $t('home.search.city') }}</span>
                <select v-model="filters.city">
                    <option value="">{{ $t('home.search.allCities') }}</option>
                    <option v-for="city in cities" :key="city.id" :value="city.id">
                        {{ city.name }}
                    </option>
                </select>
            </label>
        </div>
    </form>
</template>
