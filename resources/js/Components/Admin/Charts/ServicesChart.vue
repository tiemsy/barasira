<script setup>
import VueApexCharts from 'vue3-apexcharts'

const props = defineProps({
  categories: {
    type: Array,
    required: true,
  },
  counts: {
    type: Array,
    required: true,
  },
})

const options = {
  chart: {
    type: 'bar',
    toolbar: { show: false },
  },
  xaxis: {
    categories: props.categories,
  },
  colors: ['#0ea5e9'],
  plotOptions: {
    bar: {
      borderRadius: 6,
      columnWidth: '45%',
    },
  },
}
</script>

<template>
  <div class="card">
    <h3>Services par catégorie</h3>

    <VueApexCharts
      type="bar"
      height="300"
      :options="options"
      :series="[
        {
          name: 'Services',
          data: counts,
        }
      ]"
    />
  </div>
</template>

<style scoped>
.card {
  background: #fff;
  border-radius: 14px;
  padding: 1.5rem;
  box-shadow: 0 10px 25px rgba(0,0,0,.06);
}
</style>
