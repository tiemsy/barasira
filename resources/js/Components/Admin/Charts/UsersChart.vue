<script setup>
import VueApexCharts from 'vue3-apexcharts'

defineProps({
  data: {
    type: Object,
    required: true,
  },
})

const options = {
  chart: { type: 'donut' },
  labels: ['Admin', 'Clients', 'Prestataires'],
  legend: { position: 'bottom' },
}
</script>

<template>
  <div class="card">
    <h3>Utilisateurs par rôle</h3>

    <VueApexCharts
      width="100%"
      type="donut"
      :options="options"
      :series="[
        data.admin,
        data.client,
        data.prestataire
      ]"
    />
  </div>
</template>
