<script setup>
const props = defineProps(['results']);

const getScoreColor = (score) => {
    if (score >= 80) return "text-green-600";
    if (score >= 50) return "text-yellow-600";
    return "text-red-600";
};
</script>

<template>
    <div v-if="results" class="w-full max-w-3xl mt-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Compliance Results</h2>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-gray-700 mb-4">
                <strong>Compliance Score: </strong>
                <span :class="getScoreColor(results.compliance_score)">
                      {{ results.compliance_score }}%
                </span>
            </p>

            <!-- Iterate through issues -->
            <div v-for="(issues, category) in results.issues" :key="category" class="mb-6">
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
</template>
