<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center px-8 py-3 bg-jobnode-emerald rounded-4xl font-body text-[16px] text-black font-normal transition-all duration-200 ease-in-out hover:bg-jobnode-emeraldHover hover:shadow-l2 active:scale-98 focus:outline-none focus:ring-2 focus:ring-jobnode-emerald focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none"
    >
        <slot />
    </button>
</template>