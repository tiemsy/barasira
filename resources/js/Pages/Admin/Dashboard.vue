<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'
import StatsCard from '@/Components/Admin/StatsCard.vue'
import RecentUsers from '@/Components/Admin/RecentUsers.vue'
import RecentServices from '@/Components/Admin/RecentServices.vue'
import UsersChart from '@/Components/Admin/Charts/UsersChart.vue'
import ServicesChart from '@/Components/Admin/Charts/ServicesChart.vue'

defineProps({
    stats: Object,
    recentUsers: Array,
    recentServices: Array,
    userStats: Object,
    serviceCategories: Array,
    serviceCounts: Array,
})

const page = usePage()
// console.log(page.props[1].auth?.user);

const user = page.props[1].auth?.user || null
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout>
        <!-- HERO -->
        <section class="dashboard-hero">
            <div class="container">
                <h1 class="dashboard-title">
                    👋 Bienvenue dans le tableau de bord Administrateur {{ user?.first_name }}
                </h1>
                <p class="dashboard-subtitle">
                    Voici un aperçu de votre activité sur Barasira
                </p>
            </div>
        </section>

        <!-- CONTENT -->
        <section class="dashboard-content">
            <div class="container grid">
                <!-- STATS -->
                <section class="stats-grid">
                    <StatsCard title="Utilisateurs" :value="stats.users" icon="👤" color="primary" />
                    <StatsCard title="Prestataires" :value="stats.providers" icon="🛠️" color="success" />
                    <StatsCard title="Clients" :value="stats.clients" icon="👤" color="success" />
                    <StatsCard title="Administrateurs" :value="stats.admins" icon="👤" color="warning" />
                    <StatsCard title="Services" :value="stats.services" icon="📦" color="warning" />
                    <StatsCard title="Missions" :value="stats.missions" icon="📋" color="danger" />
                </section>

                <section class="grid two-cols">
                    <UsersChart :data="userStats" />
                    <ServicesChart :categories="serviceCategories" :counts="serviceCounts" />
                </section>

                <!-- RECENT -->
                <section class="dashboard-grid">
                    <RecentServices :services="recentServices" />
                    <RecentUsers :users="recentUsers" />
                </section>


            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
.dashboard-hero {
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #fff;
    padding: 4rem 1rem;
}

.dashboard-title {
    font-size: 2.2rem;
    font-weight: 700;
}

.dashboard-subtitle {
    margin-top: 0.5rem;
    opacity: 0.9;
}

.dashboard-content {
    padding: 4rem 1rem;
}

.grid {
    display: grid;
    gap: 2rem;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
}

.dash-card {
    background: #fff;
    border-radius: 18px;
    padding: 2rem;
    box-shadow: 0 20px 40px rgba(0, 0, 0, .08);
    transition: transform .3s ease, box-shadow .3s ease;
}

.dash-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 30px 60px rgba(0, 0, 0, .12);
}

.btn-primary {
    display: inline-block;
    margin-top: 1rem;
    background: #2563eb;
    color: #fff;
    padding: .6rem 1.4rem;
    border-radius: 999px;
    text-decoration: none;
    font-weight: 600;
}
</style>
