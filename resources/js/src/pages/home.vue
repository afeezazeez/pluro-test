<script setup>
import { ref } from 'vue';
import FileInput from '../components/ui/file-input.vue';
import Button from "../components/ui/button.vue";
import {validateFileExtension} from "../utils/fileValidation.js";
import {toaster} from "../utils/toaster.js";
import ComplianceResults from "../components/compliance-results.vue";



const selectedFile = ref(null);
const isLoading = ref(false);
const complianceResult = ref(null);


const onFileChange = (event) => {
    selectedFile.value = event.target.files[0];
};


const resetFileInput = () => {
    const fileInput = document.querySelector('input[type="file"]');
    if (fileInput) fileInput.value = '';
    selectedFile.value = null;
};

const uploadFile = async () => {
    if (!selectedFile.value) {
        toaster.error('Please select a file.');
        return;
    }

    const { valid, message } = validateFileExtension(selectedFile.value, ['html']);
    if (!valid) {
        toaster.error(message);
        resetFileInput();
        return;
    }

    isLoading.value = true;

    const formData = new FormData();
    formData.append('file', selectedFile.value);

    try {
        const {data:{data}} = await axios.post('/v1/upload', formData);
        if (data) {
            complianceResult.value = data
            toaster.success("Compliance and Accessibility analysis completed!");
            resetFileInput();
        }
    } catch (error) {
        toaster.error(error.response?.data?.message || 'An error occurred. Please refresh and upload again!');
    } finally {
        isLoading.value = false;
    }
}

</script>

<template>

    <div class="min-h-screen bg-gray-100 flex flex-col items-center p-6">

        <h1 class="text-2xl font-bold text-gray-800 mb-6">File Upload for Accessibility/Compliance Check</h1>

        <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-md">
            <FileInput :onFileChange="onFileChange" />
            <Button
                @click="uploadFile"
                :isDisabled="isLoading || !selectedFile"
                :isLoading="isLoading"
                text="Upload File"
            />
        </div>

        <div class="w-full max-w-3xl mt-8">
            <ComplianceResults :results="complianceResult" />
        </div>
    </div>
</template>


