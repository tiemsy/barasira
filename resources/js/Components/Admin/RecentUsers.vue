<script setup>
defineProps({
  users: {
    type: Array,
    required: true,
  },
})
</script>

<template>
  <div class="card">
    <div class="card-header">
      <h3>Derniers utilisateurs</h3>
    </div>

    <ul class="list">
      <li v-for="user in users" :key="user.id" class="list-item">
        <div class="avatar">
          {{ user.first_name?.charAt(0) ?? '?' }}
        </div>

        <div class="info">
          <p class="name">
            {{ user.first_name }} {{ user.last_name }}
          </p>
          <p class="meta">
            {{ user.email }} · {{ user.role }}
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

.card-header h3 {
  margin: 0 0 1rem;
  font-size: 1.1rem;
}

.list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.list-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: .75rem 0;
  border-bottom: 1px solid #f3f4f6;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #e0f2fe;
  color: #0369a1;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
}

.name {
  font-weight: 600;
  margin: 0;
}

.meta {
  font-size: .85rem;
  color: #6b7280;
  margin: 0;
}
</style>
