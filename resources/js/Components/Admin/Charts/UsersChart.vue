<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import VueApexCharts from 'vue3-apexcharts'

const props = defineProps({ data: { type: Object, default: () => ({}) } })
const { t } = useI18n()
const series = computed(() => [props.data.admin ?? 0, props.data.client ?? 0, props.data.prestataire ?? 0])
const options = computed(() => ({
    chart: { type: 'donut', fontFamily: 'inherit' },
    labels: [t('navbar.roles.admin'), t('navbar.roles.client'), t('navbar.roles.provider')],
    colors: ['#5b6472', '#177245', '#3b82f6'],
    dataLabels: { enabled: false },
    legend: { position: 'bottom', fontSize: '13px', markers: { radius: 12 } },
    stroke: { width: 4, colors: ['#fff'] },
    plotOptions: { pie: { donut: { size: '68%' } } },
    tooltip: { y: { formatter: value => `${value} ${t('adminDashboard.usersLabel')}` } },
}))
</script>

<template>
    <article class="admin-panel admin-chart-panel">
        <header class="admin-panel__header"><div><span>{{ $t('adminDashboard.analytics') }}</span><h2>{{ $t('adminDashboard.usersByRole') }}</h2></div></header>
        <VueApexCharts type="donut" height="300" :options="options" :series="series" />
    </article>
</template>
