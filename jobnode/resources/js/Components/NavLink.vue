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
        ? 'inline-flex items-center px-1 pt-1 border-b-2 border-jobnode-emerald text-sm font-medium leading-5 text-neutral-stone font-body focus:outline-none focus:border-jobnode-emerald transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-neutral-stone hover:text-neutral-stone hover:border-neutral-light focus:outline-none focus:text-neutral-stone focus:border-neutral-light transition duration-150 ease-in-out font-body',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
