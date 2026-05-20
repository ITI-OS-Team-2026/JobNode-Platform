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
        ref="input"
        v-model="model"
        class="w-full bg-transparent border-0 border-b-2 border-white text-white font-display text-[52px] leading-[57.2px] pb-4 px-0 focus:ring-0 focus:border-b-2 focus:border-jobnode-emerald focus:shadow-[0px_4px_0px_rgba(61,220,145,0.2)] placeholder:text-white/50 transition-colors duration-200"
    />
</template>