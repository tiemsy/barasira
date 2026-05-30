<template>

    <div class="hero-search">

        <!-- ===================================================== -->
        <!-- SEARCH BAR -->
        <!-- ===================================================== -->

        <div class="hero-search__wrapper">

            <!-- KEYWORD -->

            <div class="hero-search__field hero-search__field--keyword">

                <i class="bi bi-search hero-search__icon"></i>

                <input
                    v-model="filters.keyword"
                    type="text"
                    :placeholder="$t('home.searchPlaceholder')"
                    @keyup.enter="search"
                />

            </div>

            <!-- CATEGORY -->

            <div class="hero-search__field">

                <i class="bi bi-grid hero-search__icon"></i>

                <select v-model="filters.category">

                    <option value="">
                        {{ $t('home.allCategories') }}
                    </option>

                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                    >
                        {{ category.name }}
                    </option>

                </select>

            </div>

            <!-- CITY -->

            <div class="hero-search__field">

                <i class="bi bi-geo-alt hero-search__icon"></i>

                <select v-model="filters.city">

                    <option value="">
                        {{ $t('home.allCities') }}
                    </option>

                    <option
                        v-for="city in cities"
                        :key="city.id"
                        :value="city.id"
                    >
                        {{ city.name }}
                    </option>

                </select>

            </div>

            <!-- BUTTON -->

            <button
                class="hero-search__button"
                :disabled="loading"
                @click="search"
            >

                <span v-if="loading">

                    <i class="bi bi-arrow-repeat spin"></i>

                    {{ $t('common.loading') }}

                </span>

                <span v-else>

                    <i class="bi bi-search"></i>

                    {{ $t('home.search') }}

                </span>

            </button>

        </div>

        <!-- ===================================================== -->
        <!-- TAGS -->
        <!-- ===================================================== -->

        <div class="hero-search__tags">

            <button
                class="hero-search__tag"
                @click="quickSearch('Plomberie')"
            >
                🔧 Plomberie
            </button>

            <button
                class="hero-search__tag"
                @click="quickSearch('Ménage')"
            >
                🧹 Ménage
            </button>

            <button
                class="hero-search__tag"
                @click="quickSearch('Electricité')"
            >
                ⚡ Electricité
            </button>

            <button
                class="hero-search__tag"
                @click="quickSearch('Transport')"
            >
                🚚 Transport
            </button>

        </div>

    </div>

</template>

<script setup>
import { reactive, ref } from 'vue'
import axios from 'axios'

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },

    cities: {
        type: Array,
        required: true,
    },
})

const emit = defineEmits([
    'updateResults',
    'loading',
])

const loading = ref(false)

const filters = reactive({
    keyword: '',
    category: '',
    city: '',
})

const search = async () => {

    try {

        loading.value = true

        emit('loading', true)

        const response = await axios.get(
            '/api/services-search',
            {
                params: filters,
            }
        )

        emit('updateResults', response.data)

    } catch (error) {

        console.error(error)

    } finally {

        loading.value = false

        emit('loading', false)
    }
}

const quickSearch = async (keyword) => {

    filters.keyword = keyword

    await search()
}
</script>
