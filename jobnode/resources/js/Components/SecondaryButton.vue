<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-4xl border border-neutral-light bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-neutral-stone shadow-sm transition duration-150 ease-in-out hover:bg-neutral-light focus:outline-none focus:ring-2 focus:ring-jobnode-emerald focus:ring-offset-2 disabled:opacity-25 font-body"
    >
        <slot />
    </button>
</template>
