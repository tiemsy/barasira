<script setup>
defineProps({
  services: {
    type: Array,
    required: true,
  },
})

</script>

<template>
  <div class="card">
    <div class="card-header">
      <h3>Derniers services</h3>
    </div>

    <ul class="list">
      <li v-for="service in services" :key="service.id" class="list-item">
        <div class="icon">🛠️</div>

        <div class="info">
          <p class="name">{{ service.name }}</p>
          <p class="meta">
            {{ service.category?.name ?? '—' }} ·
            {{ service.city?.name ?? 'Mali' }}
          </p>
        </div>
      </li>
    </ul>
  </div>
</template>

<style scoped>
.card {
  background: #fff;
  border-radius: 14px;
  padding: 1.5rem;
  box-shadow: 0 10px 25px rgba(0,0,0,.06);
}

.icon {
  font-size: 1.5rem;
}

.list-item {
  display: flex;
  gap: 1rem;
  padding: .75rem 0;
  border-bottom: 1px solid #f3f4f6;
}
</style>
