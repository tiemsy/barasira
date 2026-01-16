<template>
  <div class="flex items-center space-x-1">
    <template v-for="star in 5" :key="star">
      <svg
        v-if="star <= rating"
        class="w-5 h-5 text-yellow-400"
        fill="currentColor"
        viewBox="0 0 20 20"
      >
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.954a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.955c.3.922-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.838-.196-1.539-1.118l1.286-3.955a1 1 0 00-.364-1.118L2.043 9.381c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.954z"/>
      </svg>
      <svg
        v-else
        class="w-5 h-5 text-gray-300"
        fill="currentColor"
        viewBox="0 0 20 20"
      >
        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.954a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.955c.3.922-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.838-.196-1.539-1.118l1.286-3.955a1 1 0 00-.364-1.118L2.043 9.381c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.954z"/>
      </svg>
    </template>
  </div>
</template>

<script setup>
defineProps({
  rating: {
    type: Number,
    required: true,
    default: 0
  }
})
</script>

<!-- A inclure dans le fichier cible : <RatingStars :rating="4" /> -->
