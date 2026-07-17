<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import VueApexCharts from 'vue3-apexcharts'

const props = defineProps({ items: { type: Array, default: () => [] } })
const { t } = useI18n()
const options = computed(() => ({
    chart: { type: 'bar', toolbar: { show: false }, fontFamily: 'inherit' },
    xaxis: { categories: props.items.map(item => item.name), labels: { trim: true, style: { colors: '#718078' } } },
    yaxis: { labels: { formatter: value => Math.round(value) } },
    colors: ['#177245'],
    dataLabels: { enabled: false },
    grid: { borderColor: '#edf1ee', strokeDashArray: 4 },
    plotOptions: { bar: { borderRadius: 7, columnWidth: '48%' } },
    tooltip: { y: { formatter: value => `${value} ${t('adminDashboard.servicesLabel')}` } },
}))
const series = computed(() => [{ name: t('adminDashboard.servicesLabel'), data: props.items.map(item => item.count) }])
</script>

<template>
    <article class="admin-panel admin-chart-panel">
        <header class="admin-panel__header"><div><span>{{ $t('adminDashboard.analytics') }}</span><h2>{{ $t('adminDashboard.servicesByCategory') }}</h2></div></header>
        <VueApexCharts type="bar" height="300" :options="options" :series="series" />
    </article>
</template>
