<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  value: {
    type: [Number, String],
    required: true,
  },
  icon: {
    type: String,
    default: '',
  },
  color: {
    type: String,
    default: 'primary', // primary | success | warning | danger
  },
})
</script>

<template>
  <div class="stats-card" :class="`stats-card--${color}`">
    <div class="stats-card__icon" v-if="icon">
      {{ icon }}
    </div>

    <div class="stats-card__content">
      <p class="stats-card__title">{{ title }}</p>
      <p class="stats-card__value">{{ value }}</p>
    </div>
  </div>
</template>
