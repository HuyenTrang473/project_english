<template>
  <component :is="layout">
    <router-view />
  </component>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import ClientLayout from './layout/ClientLayout.vue';
import AdminLayout from './layout/AdminLayout.vue';
import EmptyLayout from './layout/EmptyLayout.vue';

const route = useRoute();

const layout = computed(() => {
  const layoutName = route.meta.layout;
  if (layoutName === 'admin') return AdminLayout;
  if (layoutName === 'empty') return EmptyLayout;
  return ClientLayout; // Default to ClientLayout
});
</script>
<style></style>