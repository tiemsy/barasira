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
                    <span class="status-badge" :class="mission.status"> Status :  {{ mission.status }}</span>
                    <span> Date : {{ mission.date_start }}</span>
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
import missionService from "@/composables/missionService"

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
    ul {
        list-style: none;
        padding: 0;
        margin: 0 0 2rem 0;

        li {
            background: #fff;
            padding: 1rem 1.2rem;
            border-radius: 10px;
            margin-bottom: 0.8rem;
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
            border: 1px solid #e5e7eb;
            transition: box-shadow 0.2s ease;

            &:hover {
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            }

            strong {
                font-size: 1.05rem;
                color: #111827;
            }

            span {
                font-size: 0.9rem;
                color: #6b7280;
            }
        }
    }

    /* PAGINATION */
    .pagination {
        display: flex;
        align-items: center;
        gap: 1rem;

        button {
            padding: 0.5rem 1rem;
            background: #e5e7eb;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s ease;

            &:hover:not(:disabled) {
                background: #d1d5db;
            }

            &:disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }
        }

        span {
            font-size: 0.95rem;
            color: #374151;
            font-weight: 500;
        }
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
