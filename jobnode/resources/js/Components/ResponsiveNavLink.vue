<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-jobnode-emerald text-start text-base font-medium text-jobnode-emerald bg-neutral-light focus:outline-none focus:text-jobnode-emerald focus:bg-neutral-light focus:border-jobnode-emerald transition duration-150 ease-in-out font-body'
        : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-neutral-stone hover:text-neutral-stone hover:bg-neutral-light hover:border-neutral-light focus:outline-none focus:text-neutral-stone focus:bg-neutral-light focus:border-neutral-light transition duration-150 ease-in-out font-body',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
