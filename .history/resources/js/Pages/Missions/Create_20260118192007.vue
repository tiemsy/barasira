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
                        {{ p.name }}
                    </option>
                </select>

                <button @click="loadMissions">Filtrer</button>
            </div>

            <!-- LISTE -->
            <ul>
                <li v-for="mission in missions.data" :key="mission.id">
                    <strong>{{ mission.title }}</strong>
                    <span> - {{ mission.status }}</span>
                    <span> - {{ mission.date }}</span>
                </li>
            </ul>

            <!-- PAGINATION -->
            <div class="pagination">
                <button :disabled="!missions.prev_page_url" @click="changePage(missions.current_page - 1)">
                    Précédent
                </button>

                <span>Page {{ missions.current_page }} / {{ missions.last_page }}</span>

                <button :disabled="!missions.next_page_url" @click="changePage(missions.current_page + 1)">
                    Suivant
                </button>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { ref, onMounted } from "vue"
import missionService from "@/services/missionService"

const missions = ref({ data: [] })
const prestataires = ref([])

const filters = ref({
    status: "all",
    date: "",
    prestataire_id: ""
})

async function loadMissions(page = 1) {
    const res = await missionService.get({
        page,
        ...filters.value
    })
    missions.value = res.data
}

function changePage(page) {
    loadMissions(page)
}

onMounted(() => {
    loadMissions()
})
</script>

<style>
/* Tu peux styliser selon ton design Barasira */
</style>
