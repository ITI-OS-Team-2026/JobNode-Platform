<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-lg border-neutral-light shadow-sm font-body text-[16px] focus:border-jobnode-emerald focus:ring-jobnode-emerald focus:ring-offset-0"
        v-model="model"
        ref="input"
    />
</template>
