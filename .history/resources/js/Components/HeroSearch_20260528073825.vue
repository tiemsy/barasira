<template>
    <div class="hero-search">
        <input type="text" v-model="filters.keyword" placeholder="Quel service recherchez-vous ?" />

        <button @click="search">
            Rechercher
        </button>
    </div>

    <!-- FILTERS -->
    <div class="hero-filters">
        <select v-model="filters.category" class="hero-select">
            <option value="">Toutes les catégories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
            </option>
        </select>

        <select v-model="filters.city" class="hero-select">
            <option value="">Toutes les villes</option>
            <option v-for="city in cities" :key="city.id" :value="city.id">
                <!-- {{ city.name }} -->
            </option>
        </select>
    </div>
</template>

<script setup>
import { reactive, watch } from 'vue'
import axios from 'axios';

const props = defineProps({
    categories: {
        type: Object,
        required: true,
    },

    cities: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits(['updateResults'])

const filters = reactive({
    keyword: '',
    category: '',
    city: ''
})

// // const search = async () => {
const search = () => {


    const { data } =  axios.get('/api/services-search', {
        params: filters
    })
    emit('updateResults', data)
}

/* 🔁 Recherche live */
// const fetchServices = async () => {
//   const { data } = await axios.get('/api/services/search', {
//     params: filters
//   })
//   emit('liveResults', data)
// }

// watch(filters, fetchServices, { deep: true })
</script>
