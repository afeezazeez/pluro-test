<script setup>
import { ref } from 'vue';
import { createToaster } from "@meforma/vue-toaster";

const toaster = createToaster({
    position: 'top-right',
    duration:3000
});


const selectedFile = ref(null);
const complianceResults = ref(null);
const isLoading = ref(false);


const onFileChange = (event) => {
    selectedFile.value = event.target.files[0];
};

const getScoreColor = (score) => {
    if (score >= 80) return "text-green-600";
    if (score >= 50) return "text-yellow-600";
    return "text-red-600";
};

const resetFileInput = () => {
    const fileInput = document.querySelector('input[type="file"]');
    if (fileInput) fileInput.value = '';
    selectedFile.value = null;
};


const uploadFile = async () => {
    if (!selectedFile.value) {
        alert("Please select a file first.");
        return;
    }

    isLoading.value = true;
    complianceResults.value = null;

    const formData = new FormData();
    formData.append('file', selectedFile.value);

    try {
        const response = await axios.post('/v1/upload', formData);
        if (response.data){
            toaster.success("Compliance and Accessibility analysis completed!");
            complianceResults.value = response.data
            resetFileInput();
        }
    } catch (error) {
        toaster.error(error.response.data.message || 'An error occurred. Please refresh and upload again!');
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 flex flex-col items-center p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">File Upload for Accessibility Check</h1>

        <!-- File Input and Upload Button -->
        <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-md">
            <input
                type="file"
                @change="onFileChange"
                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer focus:outline-none focus:ring focus:ring-blue-300"
            />
            <button
                @click="uploadFile"
                :disabled="isLoading || !selectedFile"
                class="mt-4 w-full px-4 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 focus:outline-none focus:ring focus:ring-blue-300 disabled:opacity-50"
            >
                {{ isLoading ? "Uploading..." : "Upload File" }}
            </button>
        </div>

        <!-- Display Results -->
        <div v-if="complianceResults" class="w-full max-w-3xl mt-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Compliance Results</h2>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <p class="text-gray-700 mb-4">
                    <strong>Compliance Score: </strong>
                    <span :class="getScoreColor(complianceResults.data.compliance_score)">
                          {{ complianceResults.data.compliance_score }}%
                    </span>
                </p>

                <!-- Iterate through issues -->
                <div v-for="(issues, category) in complianceResults.data.issues" :key="category" class="mb-6">
                    <h3 class="text-lg font-medium text-gray-800 capitalize mb-2">{{ category.replace('_', ' ') }}</h3>
                    <template v-if="issues.length > 0">
                        <ul class="list-disc pl-5 space-y-2">
                            <li v-for="issue in issues" :key="issue.message" class="text-gray-700">
                                <p class="text-red-600 font-medium">{{ issue.message }}</p>
                                <div class="text-sm text-gray-600">
                                    <em>Element:</em> <code class="bg-gray-100 p-1 rounded">{{ issue.element }}</code><br />
                                    <em>Suggested Fix:</em> {{ issue.suggested_fix }}
                                </div>
                            </li>
                        </ul>
                    </template>
                    <template v-else>
                        <p class="text-sm text-green-800">All checks passed!</p>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

