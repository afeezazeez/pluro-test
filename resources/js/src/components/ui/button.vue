<script setup>
import { twMerge } from 'tailwind-merge';

// Props
const props = defineProps({
    type: { type: String, default: 'button' },
    isDisabled: { type: Boolean, default: false },
    isLoading: { type: Boolean, default: false },
    onClick: Function,
    classes: { type: String, default: '' },
    text: { type: String, required: true }
});

// Button click handler
const handleClick = (event) => {
    if (!props.isDisabled && props.onClick) {
        props.onClick(event);
    }
};
</script>

<template>
    <button
        :type="type"
        :disabled="isDisabled"
        :class="twMerge(
            'px-4 py-2 text-white rounded-lg focus:outline-none focus:ring',
            isDisabled
                ? 'opacity-50 cursor-not-allowed bg-gray-400'
                : 'hover:bg-blue-600 bg-blue-500 focus:ring-blue-300',
            classes
        )"
        @click="handleClick"
    >
        <span v-if="isLoading">
            Processing...
        </span>
        <span v-else>{{ text }}</span>
    </button>
</template>
