<template>

    <AppLayout :title="$t('services.title')" :subtitle="$t('services.subtitle')">

        <div class="services-page">

            <!-- ===================================================== -->
            <!-- HERO -->
            <!-- ===================================================== -->

            <section class="services-hero">

                <div class="container">

                    <div class="services-hero__content">

                        <span class="services-hero__badge">
                            🔥 {{ $t('services.findTrustedProviders') }}
                        </span>

                        <h1 class="services-hero__title">
                            {{ $t('services.heroTitle') }}
                        </h1>

                        <p class="services-hero__subtitle">
                            {{ $t('services.heroSubtitle') }}
                        </p>

                    </div>

                </div>

            </section>

            <!-- ===================================================== -->
            <!-- SEARCH -->
            <!-- ===================================================== -->

            <section class="services-search-section">

                <div class="container">

                    <div class="services-filters">

                        <!-- KEYWORD -->

                        <div class="services-filter">

                            <i class="bi bi-search"></i>

                            <input v-model="keyword" type="text" :placeholder="$t('services.searchPlaceholder')"
                                @keyup.enter="fetchServices(true)" />

                        </div>

                        <!-- CITY -->

                        <div class="services-filter">

                            <i class="bi bi-geo-alt"></i>

                            <select v-model="city" @change="fetchServices(true)">

                                <option :value="null">
                                    {{ $t('services.allCities') }}
                                </option>

                                <option v-for="city in $page.props.cities" :key="city.id" :value="city.id">
                                    {{ city.name }}
                                </option>

                            </select>

                        </div>

                        <!-- CATEGORY -->

                        <div class="services-filter">

                            <i class="bi bi-grid"></i>

                            <select v-model="category" @change="fetchServices(true)">

                                <option :value="null">
                                    {{ $t('services.allCategories') }}
                                </option>

                                <option v-for="cat in $page.props.categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>

                            </select>

                        </div>

                        <!-- BUTTON -->

                        <button class="services-search-btn" :disabled="loading" @click="fetchServices(true)">

                            <span v-if="loading">

                                <i class="bi bi-arrow-repeat spin"></i>

                                {{ $t('common.loading') }}

                            </span>

                            <span v-else>

                                <i class="bi bi-search"></i>

                                {{ $t('services.search') }}

                            </span>

                        </button>

                    </div>

                </div>

            </section>

            <!-- ===================================================== -->
            <!-- TOPBAR -->
            <!-- ===================================================== -->

            <section class="services-topbar">

                <div class="container">

                    <div class="services-topbar__content">

                        <div class="services-results">

                            <strong>
                                {{ services.length }}
                            </strong>

                            {{ $t('services.resultsFound') }}

                        </div>

                        <div class="services-sort">

                            <label>
                                {{ $t('services.sortBy') }}
                            </label>

                            <select v-model="sort">

                                <option value="latest">
                                    {{ $t('services.latest') }}
                                </option>

                                <option value="price_asc">
                                    {{ $t('services.priceLowHigh') }}
                                </option>

                                <option value="price_desc">
                                    {{ $t('services.priceHighLow') }}
                                </option>

                            </select>

                        </div>

                    </div>

                </div>

            </section>

            <!-- ===================================================== -->
            <!-- GRID -->
            <!-- ===================================================== -->

            <section class="services-content">

                <div class="container">

                    <!-- EMPTY -->

                    <div v-if="!loading && services.length === 0" class="services-empty">

                        <div class="services-empty__icon">
                            🔎
                        </div>

                        <h2 class="services-empty__title">
                            {{ $t('services.noServices') }}
                        </h2>

                        <p class="services-empty__text">
                            {{ $t('services.tryAnotherSearch') }}
                        </p>

                    </div>

                    <!-- GRID -->

                    <div v-else class="services-grid">

                        <ServiceCard v-for="service in services" :key="service.id" :service="service" />

                    </div>

                    <!-- LOADER -->

                    <div v-if="loading" class="services-loader">

                        <div class="services-loader__spinner"></div>

                        <p>
                            {{ $t('common.loading') }}
                        </p>

                    </div>

                    <!-- LOAD MORE -->

                    <div v-if="hasMore && services.length" class="services-load-more">

                        <button class="btn btn-primary" :disabled="loading" @click="fetchServices()">

                            <span v-if="loading">

                                <i class="bi bi-arrow-repeat spin"></i>

                                {{ $t('common.loading') }}

                            </span>

                            <span v-else>

                                {{ $t('services.loadMore') }}

                            </span>

                        </button>

                    </div>

                </div>

            </section>

        </div>

    </AppLayout>

</template>

<script setup>
import { onMounted, ref, watch } from 'vue'

import AppLayout from '@/Layouts/AppLayout.vue'
import ServiceCard from '@/Components/ServiceCard.vue'

import { api } from '@/lib/api'

const services = ref([])

const page = ref(1)

const loading = ref(false)

const hasMore = ref(true)

const keyword = ref('')

const city = ref(null)

const category = ref(null)

const sort = ref('latest')

const fetchServices = async (reset = false) => {

    if (loading.value) {
        return
    }

    if (!hasMore.value && !reset) {
        return
    }

    try {

        loading.value = true

        if (reset) {

            services.value = []

            page.value = 1

            hasMore.value = true
        }

        const response = await api.get(
            '/services-search',
            {
                params: {
                    page: page.value,
                    keyword: keyword.value || undefined,
                    city: city.value || undefined,
                    category: category.value || undefined,
                    sort: sort.value || undefined,
                },
            }
        )

        const results = response.data?.data || response.data

        if (!results.length) {

            hasMore.value = false

        } else {

            services.value.push(...results)

            page.value++
        }

    } catch (error) {

        console.error(error)

    } finally {

        loading.value = false
    }
}

/* =========================================================
   AUTO SEARCH ON SORT
========================================================= */

watch(sort, () => {

    fetchServices(true)
})

/* =========================================================
   INIT
========================================================= */

onMounted(() => {

    fetchServices()
})
</script>
