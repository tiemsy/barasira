<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, usePage } from '@inertiajs/vue3'

defineProps({
    services: Array,
})



const page = usePage()

const user = page.props[1].auth?.user
</script>

<template>

    <Head title="Dashboard" />

    <AppLayout>
        <!-- HERO -->
        <section class="dashboard-hero">
            <div class="container">
                <h1 class="dashboard-title">
                    👋 Bienvenue dans l'espace client {{ user?.first_name }}
                </h1>
                <p class="dashboard-subtitle">
                    Voici un aperçu de vos derniers services sur Barasira
                </p>
            </div>
        </section>

        <!-- CONTENT -->
        <section class="dashboard-content">
            <div class="container grid">

                <!-- CARD -->
                <div class="dash-card">
                    <h3>📦 Mes services acceptés</h3>
                    <ul>
                        <li v-for="service in services" :key="service.id">
                            {{ service.name }}
                            <span v-if="service.category">- {{ service.category?.name }}</span>
                        </li>
                    </ul>
                </div>

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
