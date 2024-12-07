<script setup>
import { ref } from 'vue';


const props = defineProps({
    onFileChange: {
        type: Function,
        required: true,
    }
});

const fileInput = ref(null);


const handleFileChange = (event) => {
    if (props.onFileChange) {
        props.onFileChange(event);
    }
};
</script>

<template>
    <div class="mb-4">
        <input
            ref="fileInput"
            type="file"
            @change="handleFileChange"
        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer focus:outline-none focus:ring focus:ring-blue-300"
        />
    </div>
</template>
