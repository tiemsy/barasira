<template>
    <AppLayout>
        <div class="missions">

            <h2>Gestion des missions</h2>

            <!-- FILTRES -->
            <div class="filters">
                <select v-model="filters.status">
                    <option value="all">Tous les statuts</option>
                    <option value="pending">En attente</option>
                    <option value="in_progress">En cours</option>
                    <option value="completed">Terminée</option>
                    <option value="cancelled">Annulée</option>
                </select>

                <input type="date" v-model="filters.date" />

                <select v-model="filters.prestataire_id">
                    <option value="">Tous les prestataires</option>
                    <option v-for="p in prestataires" :key="p.id" :value="p.id">
                        {{ p.first_name }} {{ p.last_name }}
                    </option>
                </select>

                <button @click="loadMissions">Filtrer</button>
            </div>

            <!-- LISTE -->
            <!-- <ul id="missions"></ul>
            <div id="loader" style="text-align:center; padding:20px;">Chargement...</div> -->
            <!-- <ul>
                <li v-for="mission in missions.data" :key="mission.id">
                    <strong>{{ mission.title }}</strong>
                    <span class="status-badge" :class="mission.status"> Status :  {{ mission.status }}</span>
                    <span> Date : {{ mission.date_start }}</span>
                </li>
            </ul> -->

            <div class="missions-container">
                <div v-for="mission in missions" :key="mission.id" class="mission-card">
                    <h3>{{ mission.titre }}</h3>
                    <p>{{ mission.description }}</p>
                </div>

                <div ref="loadMoreTrigger" class="loader">
                    <span v-if="loading">Chargement...</span>
                    <span v-else-if="noMore">Fin des missions</span>
                </div>
            </div>

            <!-- PAGINATION -->
            <!-- <div class="pagination">
                <button :disabled="!missions.prev_page_url" @click="changePage(missions.current_page - 1)">
                    Précédent
                </button>

                <span>Page {{ missions.current_page }} / {{ missions.last_page }}</span>

                <button :disabled="!missions.next_page_url" @click="changePage(missions.current_page + 1)">
                    Suivant
                </button>
            </div> -->

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted, onBeforeUnmount } from "vue"
import missionService from "@/composables/missionService"

// const missions = ref({ data: [] })
// const prestataires = ref({ data: [] })
defineProps({
    prestataires: Object
})

const filters = ref({
    status: "all",
    date: "",
    prestataire_id: ""
})

const missions = ref([])
const page = ref(1)
const loading = ref(false)
const noMore = ref(false)
const loadMoreTrigger = ref(null)

let observer = null


const loadMissions = async () => {
    if (loading.value || noMore.value) return
    loading.value = true

    const response = await missionService.get({
        page: page.value,
        ...filters.value
    })
    console.log(response)
    const data = await response.json();

    missions.value.push(...data.data)

    if (!data.next_page_url) {
        noMore.value = true
    } else {
        page.value++
    }

    loading.value = false
    // missions.value = res.data
}


function changePage(page) {
    loadMissions(page)
}

onMounted(() => {
    loadMissions()
    observer = new IntersectionObserver(entries => {
        if (entries[0].isIntersecting) {
            loadMissions()
        }
    })

    if (loadMoreTrigger.value) {
        observer.observe(loadMoreTrigger.value)
    }
})

onBeforeUnmount(() => {
    if (observer && loadMoreTrigger.value) {
        observer.unobserve(loadMoreTrigger.value)
    }
})
</script>

<style lang="scss" scoped>
.missions {
    padding: 2rem;
    background: #f9fafb;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);

    h2 {
        font-size: 1.6rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #111827;
    }

    /* FILTRES */
    .filters {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;

        select,
        input[type="date"] {
            padding: 0.6rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fff;
            font-size: 0.95rem;
            color: #374151;
            transition: border-color 0.2s ease;

            &:focus {
                border-color: #2563eb;
                outline: none;
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
            }
        }

        button {
            padding: 0.6rem 1.2rem;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s ease;

            &:hover {
                background: #1e4fc7;
            }
        }
    }

    /* LISTE DES MISSIONS */

    .missions-container {
        max-width: 600px;
        margin: auto;
    }

    .mission-card {
        padding: 15px;
        margin-bottom: 10px;
        background: #f5f5f5;
        border-radius: 8px;
    }

    .loader {
        text-align: center;
        padding: 20px;
        color: #666;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 6px;
        text-transform: capitalize;
        color: #fff;
        letter-spacing: 0.3px;

        &.pending {
            background-color: #f59e0b; // jaune
        }

        &.in_progress,
        &.in-progress {
            background-color: #3b82f6; // bleu
        }

        &.completed {
            background-color: #10b981; // vert
        }

        &.cancelled {
            background-color: #ef4444; // rouge
        }
    }

}
</style>
