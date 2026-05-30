<template>
    <AppLayout>
        <div class="services-page">

            <!-- HERO -->
            <section class="services-hero">
                <!--<h1>Trouver un service</h1>-->

                <div class="hero-search">
                    <!-- <input v-model="keyword" placeholder="Plombier, électricien..." @input="fetchServices(true)" />
                    <button @click="fetchServices(true)">Rechercher</button> -->
                </div>
            </section>

            <!-- FILTRES -->
            <section class="services-filters">
                <select v-model="city" @change="fetchServices(true)">
                    <option :value="null">Toutes les villes</option>
                    <option v-for="city in $page.props.cities" :key="city.id" :value="city.id">
                        {{ city.name }}
                    </option>
                </select>

                <select v-model="category" @change="fetchServices(true)">
                    <option :value="null">Toutes les catégories</option>
                    <option v-for="cat in $page.props.categories" :key="cat.id" :value="cat.id">
                        {{ cat.name }}
                    </option>
                </select>
            </section>

            <!-- LISTE -->
            <section class="services-grid">
                <ServiceCard v-for="service in services" :key="service.id" :service="service" />
            </section>

            <!-- LOADER -->
            <!--<div class="load-more" v-if="hasMore">
                <button @click="fetchServices()">Charger plus</button>
            </div>-->

        </div>
    </AppLayout>
</template>


<script setup>
import { ref, onMounted } from 'vue'
import { api } from '@/lib/api'
import AppLayout from '@/Layouts/AppLayout.vue'
import ServiceCard from '@/Components/ServiceCard.vue'

const services = ref([])
const page = ref(1)
const loading = ref(false)
const hasMore = ref(true)

const keyword = ref('')
const city = ref(null)
const category = ref(null)

const fetchServices = async (reset = false) => {
    if (loading.value || !hasMore.value) return

    loading.value = true

    if (reset) {
        services.value = []
        page.value = 1
        hasMore.value = true
    }

    const { data } = await api.get('/services-search', {
        params: {
            page: page.value,
            keyword: keyword.value || undefined,
            city: city.value || undefined,
            category: category.value || undefined,
        }
    })

    if (data.length === 0) {
        hasMore.value = false
    } else {
        services.value.push(...data)
        page.value++
    }

    loading.value = false
}

onMounted(() => fetchServices())
</script>
