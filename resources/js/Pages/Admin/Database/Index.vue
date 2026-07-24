<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DashboardIcon from '@/Components/DashboardIcon.vue'

const props = defineProps({
    tables: { type: Array, default: () => [] },
    selectedTable: { type: String, default: null },
    columns: { type: Array, default: () => [] },
    rows: { type: Object, default: null },
    perPage: { type: Number, default: 50 },
})

const table = ref(props.selectedTable)
const perPage = ref(props.perPage)

function load() {
    router.get('/admin/database', { table: table.value, per_page: perPage.value }, { preserveState: true, replace: true })
}

function selectTable(name) {
    table.value = name
    load()
}

function display(value) {
    if (value === null) return 'NULL'
    if (value === '') return '∅'
    return typeof value === 'object' ? JSON.stringify(value) : String(value)
}
</script>

<template>
    <Head :title="$t('adminDatabase.metaTitle')" />
    <AppLayout>
        <main class="database-browser">
            <header class="database-browser__hero">
                <div><span><DashboardIcon name="storage" />{{ $t('adminDatabase.eyebrow') }}</span><h1>{{ $t('adminDatabase.title') }}</h1><p>{{ $t('adminDatabase.subtitle') }}</p></div>
                <Link href="/admin/dashboard"><DashboardIcon name="arrow-left" />{{ $t('adminDatabase.back') }}</Link>
            </header>

            <section class="database-browser__workspace">
                <aside>
                    <strong>{{ $t('adminDatabase.tables', { count: tables.length }) }}</strong>
                    <nav><button v-for="name in tables" :key="name" type="button" :class="{ active: name === table }" @click="selectTable(name)">{{ name }}</button></nav>
                </aside>

                <article>
                    <header>
                        <div><small>{{ $t('adminDatabase.selectedTable') }}</small><h2>{{ selectedTable || '—' }}</h2></div>
                        <label>{{ $t('adminDatabase.perPage') }}<select v-model.number="perPage" @change="load"><option v-for="size in [25, 50, 100]" :key="size" :value="size">{{ size }}</option></select></label>
                    </header>
                    <div class="database-browser__meta"><span>{{ $t('adminDatabase.columns', { count: columns.length }) }}</span><span>{{ $t('adminDatabase.rows', { count: rows?.total ?? 0 }) }}</span><span><DashboardIcon name="lock" />{{ $t('adminDatabase.readOnly') }}</span></div>

                    <section class="database-browser__structure">
                        <h3>{{ $t('adminDatabase.structure') }}</h3>
                        <div><article v-for="column in columns" :key="column.name"><strong>{{ column.name }}</strong><code>{{ column.type }}</code><small>{{ column.nullable ? $t('adminDatabase.nullable') : $t('adminDatabase.required') }}</small></article></div>
                    </section>

                    <div v-if="rows?.data?.length" class="database-browser__table-wrap">
                        <table><thead><tr><th v-for="column in columns" :key="column.name"><strong>{{ column.name }}</strong><small>{{ column.type }}{{ column.nullable ? ' · NULL' : '' }}</small></th></tr></thead>
                        <tbody><tr v-for="(row, index) in rows.data" :key="row.id ?? index"><td v-for="column in columns" :key="column.name" :class="{ masked: row[column.name] === '[MASQUÉ]' }"><code>{{ display(row[column.name]) }}</code></td></tr></tbody></table>
                    </div>
                    <div v-else class="database-browser__empty"><DashboardIcon name="storage" /><p>{{ $t('adminDatabase.empty') }}</p></div>

                    <nav v-if="rows?.links?.length > 3" class="database-browser__pagination"><Link v-for="link in rows.links" :key="link.label" :href="link.url || ''" :class="{ active: link.active, disabled: !link.url }" preserve-scroll v-html="link.label" /></nav>
                </article>
            </section>
        </main>
    </AppLayout>
</template>

<style scoped>
.database-browser{min-height:100vh;padding:3rem max(1rem,calc((100vw - 1400px)/2)) 4rem;background:#f4f7f5;color:#17251e}.database-browser__hero{display:flex;align-items:flex-end;justify-content:space-between;gap:2rem;margin-bottom:1rem;padding:2rem;border-radius:20px;background:linear-gradient(125deg,#0b3522,#176c43);color:#fff}.database-browser__hero span,.database-browser__hero>a{display:inline-flex;align-items:center;gap:.5rem}.database-browser__hero h1{margin:.5rem 0}.database-browser__hero p{margin:0;color:#d5e8dd}.database-browser__hero>a{padding:.75rem 1rem;border-radius:9px;background:#fff;color:#145c39;font-weight:800;text-decoration:none}.database-browser__workspace{display:grid;grid-template-columns:240px minmax(0,1fr);gap:1rem}.database-browser__workspace>aside,.database-browser__workspace>article{min-width:0;border:1px solid #dfe8e2;border-radius:16px;background:#fff;overflow:hidden}.database-browser__workspace>aside{align-self:start;padding:.8rem}.database-browser__workspace>aside>strong{display:block;padding:.6rem}.database-browser__workspace aside nav{max-height:70vh;overflow:auto}.database-browser__workspace aside button{width:100%;padding:.65rem;border:0;border-radius:8px;background:transparent;text-align:left;cursor:pointer}.database-browser__workspace aside button:hover,.database-browser__workspace aside button.active{background:#eaf6ef;color:#12633c;font-weight:800}.database-browser__workspace article>header{display:flex;align-items:end;justify-content:space-between;padding:1rem 1.2rem;border-bottom:1px solid #e5ece7}.database-browser__workspace h2{margin:.2rem 0 0}.database-browser__workspace label{display:grid;gap:.25rem;font-size:.75rem}.database-browser__workspace select{padding:.5rem;border:1px solid #ccd8d1;border-radius:8px}.database-browser__meta{display:flex;gap:1rem;padding:.65rem 1.2rem;background:#17251e;color:#c2d3c9;font-size:.75rem}.database-browser__meta span{display:inline-flex;align-items:center;gap:.35rem}.database-browser__table-wrap{overflow:auto;max-height:68vh}table{border-collapse:collapse;min-width:100%;font-size:.78rem}th,td{max-width:420px;padding:.65rem .8rem;border-right:1px solid #e3eae6;border-bottom:1px solid #e3eae6;text-align:left;vertical-align:top}th{position:sticky;z-index:1;top:0;background:#edf5f0}th small{display:block;margin-top:.2rem;color:#75837a;font-weight:400}td code{display:block;overflow:hidden;max-height:7rem;color:#293b31;white-space:pre-wrap;overflow-wrap:anywhere}.masked code{color:#a35b2f;font-weight:800}.database-browser__empty{display:grid;min-height:300px;place-items:center;align-content:center;color:#718078}.database-browser__pagination{display:flex;justify-content:center;gap:.3rem;padding:1rem}.database-browser__pagination a{padding:.45rem .65rem;border-radius:7px;color:#176b45;text-decoration:none}.database-browser__pagination a.active{background:#176b45;color:#fff}.database-browser__pagination a.disabled{pointer-events:none;opacity:.4}@media(max-width:800px){.database-browser__hero{align-items:stretch;flex-direction:column}.database-browser__workspace{grid-template-columns:1fr}.database-browser__workspace aside nav{display:flex;max-height:none;overflow:auto}.database-browser__workspace aside button{width:auto;white-space:nowrap}}
.database-browser__structure{padding:1rem 1.2rem;border-bottom:1px solid #e3eae6}.database-browser__structure h3{margin:0 0 .7rem;font-size:.9rem}.database-browser__structure>div{display:flex;gap:.5rem;overflow:auto;padding-bottom:.25rem}.database-browser__structure article{display:grid;flex:0 0 auto;min-width:145px;padding:.65rem .75rem;border:1px solid #dce7e0;border-radius:9px;background:#f7faf8}.database-browser__structure article code{margin:.2rem 0;color:#176b45}.database-browser__structure article small{color:#718078}
</style>
