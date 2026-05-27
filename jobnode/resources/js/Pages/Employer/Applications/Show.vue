<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { computed, ref, toRefs } from "vue";
import { 
    CheckCircleIcon, 
    LockClosedIcon, 
    ShieldCheckIcon, 
    BanknotesIcon,
    XMarkIcon 
} from "@heroicons/vue/24/solid";

const props = defineProps({
    application: Object,
    candidate: Object,
    profile: [Object, null],
    job: Object,
    isUnlocked: Boolean,
    unlockPrice: Number,
    unlockCurrency: String,
    unlockDetails: [Object, null],
    paymentProvider: { type: String, default: "kashier" },
    resumeDownloadUrl: { type: [String, null], default: null },
});

const {
    application,
    candidate,
    profile,
    job,
    isUnlocked,
    unlockPrice,
    unlockCurrency,
    unlockDetails,
    paymentProvider,
    resumeDownloadUrl,
} = toRefs(props);

const completenessStatus = computed(() => {
    if (!profile.value) return "Incomplete";
    const percentage = profile.value.completeness_percentage ?? 0;
    if (percentage === 100) return "Complete";
    if (percentage >= 75) return "Almost Complete";
    if (percentage >= 50) return "Partially Complete";
    return "Incomplete";
});

const completenessStatusClass = computed(() => {
    if (!profile.value) return "text-red-600 bg-red-50";
    const percentage = profile.value.completeness_percentage ?? 0;
    if (percentage === 100) return "text-green-600 bg-green-50";
    if (percentage >= 75) return "text-yellow-600 bg-yellow-50";
    if (percentage >= 50) return "text-orange-600 bg-orange-50";
    return "text-red-600 bg-red-50";
});

const getStatusColor = (status) => {
    switch (status) {
        case "applied":
            return "bg-teal-100 text-teal-800";
        case "reviewed":
            return "bg-emerald-100 text-emerald-800";
        case "shortlisted":
            return "bg-green-100 text-green-800";
        case "rejected":
            return "bg-red-100 text-red-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const loading = ref(false);
const error = ref(null);
const isModalOpen = ref(false);

const csrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");

function submitFormFallback(endpoint) {
    const form = document.createElement("form");
    form.method = "POST";
    form.action = endpoint;

    const csrfInput = document.createElement("input");
    csrfInput.type = "hidden";
    csrfInput.name = "_token";
    csrfInput.value = csrfToken() || "";
    form.appendChild(csrfInput);

    const providerInput = document.createElement("input");
    providerInput.type = "hidden";
    providerInput.name = "provider";
    providerInput.value = paymentProvider.value;
    form.appendChild(providerInput);

    document.body.appendChild(form);
    form.submit();
}

async function startUnlock() {
    error.value = null;
    loading.value = true;

    const endpoint = route(
        "employer.applications.payments.store",
        application.value.id,
    );

    try {
        const res = await fetch(endpoint, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": csrfToken(),
            },
            body: JSON.stringify({ provider: paymentProvider.value }),
            credentials: "same-origin",
        });

        const contentType = res.headers.get("content-type") || "";

        if (contentType.includes("application/json")) {
            const data = await res.json();

            if (!res.ok) {
                error.value = data.message || "Payment creation failed. Gateway unavailable.";
                loading.value = false;
                return;
            }

            if (data.sessionUrl) {
                window.location.href = data.sessionUrl;
                return;
            }

            if (data.id) {
                submitFormFallback(endpoint);
                return;
            }
        }

        if (res.redirected && res.url) {
            window.location.href = res.url;
            return;
        }

        submitFormFallback(endpoint);
    } catch (e) {
        error.value = "Network error. Gateway unavailable or offline.";
        submitFormFallback(endpoint);
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <Head title="Application Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-display font-semibold text-xl text-gray-800 leading-tight">
                    Application Details
                </h2>
                <Link
                    :href="route('employer.applications.index')"
                    class="text-emerald-600 hover:text-emerald-800 font-semibold transition-colors"
                >
                    Back to Applications
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Flash Messages -->
                <div v-if="$page.props.flash && $page.props.flash.success" class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-sm flex items-center gap-3">
                    <CheckCircleIcon class="w-6 h-6 text-green-500" />
                    <div>
                        <p class="font-bold">Payment completed successfully.</p>
                        <p class="text-sm">{{ $page.props.flash.success }}</p>
                    </div>
                </div>
                
                <div v-if="$page.props.flash && $page.props.flash.error" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-sm">
                    <p class="font-semibold">{{ $page.props.flash.error }}</p>
                </div>

                <!-- Unlocked Banner -->
                <div v-if="isUnlocked" class="bg-green-50 border border-green-200 rounded-xl p-5 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="bg-green-100 p-2 rounded-full">
                            <CheckCircleIcon class="w-6 h-6 text-green-600" />
                        </div>
                        <div>
                            <p class="text-green-900 font-bold text-lg">Candidate Unlocked</p>
                            <p class="text-green-700 text-sm">You have permanent access to all contact information.</p>
                        </div>
                    </div>
                    <div v-if="unlockDetails" class="hidden sm:block text-right">
                        <p class="text-green-800 text-sm font-medium">Unlocked on:</p>
                        <p class="text-green-900 font-bold">{{ unlockDetails.unlocked_at }}</p>
                    </div>
                </div>

                <!-- Application Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-display text-3xl font-bold text-gray-900 mb-2">
                                {{ candidate.name }}
                            </h3>
                            <p class="text-gray-600">
                                Applied for:
                                <span class="font-semibold text-gray-900">{{ job.title }}</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <span :class="`px-4 py-1.5 rounded-full text-sm font-bold tracking-wide uppercase ${getStatusColor(application.status)}`">
                                {{ application.status }}
                            </span>
                            <p class="text-sm text-gray-500 mt-3 font-medium">
                                {{ formatDate(application.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column: Profile & Resume -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Contact & Resume Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-8">
                            <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
                                <h3 class="font-display text-xl font-bold text-gray-900">
                                    Contact &amp; Resume
                                </h3>
                                <span v-if="isUnlocked" class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-bold">
                                    <CheckCircleIcon class="w-4 h-4" />
                                    Unlocked
                                </span>
                                <span v-else class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-bold tracking-wide">
                                    <LockClosedIcon class="w-4 h-4" />
                                    Locked
                                </span>
                            </div>

                            <div class="space-y-8">
                                <!-- Email -->
                                <div>
                                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">Email Address</p>
                                    <div v-if="isUnlocked && candidate.email" class="flex items-center text-gray-900 font-medium text-lg">
                                        {{ candidate.email }}
                                    </div>
                                    <div v-else class="flex items-center gap-3">
                                        <div class="text-gray-400 font-mono text-lg select-none filter blur-[4px]">john*****@gmail.com</div>
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div>
                                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">Phone Number</p>
                                    <div v-if="isUnlocked && profile && profile.phone" class="flex items-center text-gray-900 font-medium text-lg">
                                        {{ profile.phone }}
                                    </div>
                                    <div v-else class="flex items-center gap-3">
                                        <div class="text-gray-400 font-mono text-lg select-none filter blur-[4px]">+20 10 *** ****</div>
                                    </div>
                                </div>

                                <!-- Resume -->
                                <div>
                                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-3">Resume Document</p>
                                    
                                    <!-- Unlocked and resume exists -->
                                    <div v-if="isUnlocked && profile && profile.resume_path" class="flex flex-col sm:flex-row sm:items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-200 gap-4">
                                        <div>
                                            <p class="text-gray-900 font-bold mb-1">{{ profile.resume_original_filename || "Resume.pdf" }}</p>
                                            <p v-if="profile.resume_uploaded_at" class="text-xs text-gray-500 font-medium">Uploaded: {{ new Date(profile.resume_uploaded_at).toLocaleDateString() }}</p>
                                        </div>
                                        <a v-if="resumeDownloadUrl" :href="resumeDownloadUrl" class="inline-flex items-center justify-center px-4 py-2 bg-white border-2 border-emerald-600 text-emerald-700 rounded-lg text-sm font-bold hover:bg-emerald-50 hover:border-emerald-700 transition-colors shadow-sm">
                                            Download Resume
                                        </a>
                                    </div>

                                    <!-- No resume provided -->
                                    <div v-else-if="!profile || !profile.has_resume" class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                        <p class="text-gray-500 italic font-medium">No resume provided by candidate.</p>
                                    </div>

                                    <!-- Locked but resume exists -->
                                    <div v-else class="flex items-center gap-3">
                                        <div class="text-gray-400 font-mono text-lg select-none filter blur-[3px]">Resume_Document.pdf</div>
                                        <LockClosedIcon class="w-5 h-5 text-gray-400" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment / Unlock Card -->
                        <div v-if="!isUnlocked" class="bg-gradient-to-br from-emerald-50 to-white shadow-sm sm:rounded-2xl border-2 border-emerald-100 overflow-hidden">
                            <div class="p-8">
                                <div class="flex flex-col md:flex-row gap-8 items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-display text-2xl font-bold text-gray-900 mb-3">Unlock Candidate Access</h3>
                                        <p class="text-gray-600 mb-6 leading-relaxed">
                                            Candidate information is protected. Unlock this candidate to view contact information and download the resume.
                                        </p>
                                        
                                        <ul class="space-y-3">
                                            <li class="flex items-center gap-3 text-gray-800 font-medium">
                                                <CheckCircleIcon class="w-5 h-5 text-emerald-600" />
                                                <span>Candidate Email Address</span>
                                            </li>
                                            <li class="flex items-center gap-3 text-gray-800 font-medium">
                                                <CheckCircleIcon class="w-5 h-5 text-emerald-600" />
                                                <span>Candidate Phone Number</span>
                                            </li>
                                            <li class="flex items-center gap-3 text-gray-800 font-medium">
                                                <CheckCircleIcon class="w-5 h-5 text-emerald-600" />
                                                <span>Full Resume Download</span>
                                            </li>
                                            <li class="flex items-center gap-3 text-gray-800 font-medium">
                                                <CheckCircleIcon class="w-5 h-5 text-emerald-600" />
                                                <span>Permanent Access</span>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm w-full md:w-72 text-center flex flex-col justify-center">
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">One-time payment</p>
                                        <p class="text-4xl font-black text-gray-900 mb-6">{{ unlockCurrency }} {{ unlockPrice }}</p>
                                        
                                        <button 
                                            @click="isModalOpen = true" 
                                            class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-lg rounded-xl shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all transform hover:-translate-y-0.5"
                                        >
                                            Unlock Access
                                        </button>
                                        
                                        <div class="mt-5 flex items-center justify-center gap-1.5 text-xs font-medium text-gray-400">
                                            <ShieldCheckIcon class="w-4 h-4" />
                                            <span>Secure payment powered by Kashier</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Summary (When Unlocked) -->
                        <div v-if="isUnlocked && unlockDetails" class="bg-gray-50 shadow-sm sm:rounded-2xl border border-gray-200 overflow-hidden">
                            <div class="px-8 py-6">
                                <h3 class="font-display text-lg font-bold text-gray-900 mb-6 flex items-center gap-2 border-b border-gray-200 pb-4">
                                    <BanknotesIcon class="w-5 h-5 text-gray-500" />
                                    Payment Summary
                                </h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                    <div>
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Status</p>
                                        <span class="text-sm font-bold text-green-700 bg-green-100 px-2.5 py-1 rounded-md">Paid</span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Provider</p>
                                        <p class="text-sm font-bold text-gray-900">{{ unlockDetails.provider }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Amount</p>
                                        <p class="text-sm font-bold text-gray-900">{{ unlockDetails.currency }} {{ unlockDetails.amount }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Access</p>
                                        <p class="text-sm font-bold text-gray-900">Permanent</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Candidate Profile -->
                    <div class="space-y-6">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-6">
                            <h3 class="font-display text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">
                                Candidate Profile
                            </h3>

                            <div v-if="!profile" class="text-center py-6">
                                <p class="text-gray-500 font-medium">No profile information available.</p>
                            </div>

                            <div v-else class="space-y-6">
                                <!-- Completeness -->
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wide">Completeness</p>
                                        <p class="text-lg font-black" :class="profile.completeness_percentage === 100 ? 'text-green-600' : 'text-orange-600'">
                                            {{ profile.completeness_percentage }}%
                                        </p>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2.5 mb-2">
                                        <div class="h-2.5 rounded-full transition-all"
                                            :class="profile.completeness_percentage === 100 ? 'bg-green-500' : 'bg-orange-400'"
                                            :style="`width: ${profile.completeness_percentage}%`">
                                        </div>
                                    </div>
                                </div>

                                <!-- Title -->
                                <div>
                                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-1">Professional Title</p>
                                    <p v-if="profile.title" class="text-gray-900 font-medium">{{ profile.title }}</p>
                                    <p v-else class="text-gray-400 italic font-medium">Not provided</p>
                                </div>

                                <!-- Skills -->
                                <div>
                                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-2">Skills</p>
                                    <div v-if="profile.skills && profile.skills.length > 0" class="flex flex-wrap gap-2">
                                        <span v-for="(skill, index) in profile.skills" :key="index" class="inline-flex px-2.5 py-1 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-md text-xs font-bold uppercase tracking-wide">
                                            {{ skill }}
                                        </span>
                                    </div>
                                    <p v-else class="text-gray-400 italic font-medium">Not provided</p>
                                </div>

                                <!-- LinkedIn -->
                                <div>
                                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-1">LinkedIn Profile</p>
                                    <div v-if="profile.linkedin_url">
                                        <a :href="profile.linkedin_url" target="_blank" rel="noopener noreferrer" class="text-emerald-600 hover:text-emerald-800 font-medium break-all underline decoration-emerald-300 underline-offset-2">
                                            {{ profile.linkedin_url }}
                                        </a>
                                    </div>
                                    <p v-else class="text-gray-400 italic font-medium">Not provided</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Confirmation Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" @click="!loading && (isModalOpen = false)" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-8">
                    <div class="absolute top-0 right-0 pt-5 pr-5">
                        <button type="button" @click="isModalOpen = false" :disabled="loading" class="bg-white rounded-md text-gray-400 hover:text-gray-600 focus:outline-none disabled:opacity-50 transition-colors">
                            <span class="sr-only">Close</span>
                            <XMarkIcon class="h-6 w-6" />
                        </button>
                    </div>
                    
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-full bg-emerald-50 border-4 border-emerald-100 sm:mx-0 sm:h-12 sm:w-12">
                            <LockClosedIcon class="h-6 w-6 text-emerald-600" aria-hidden="true" />
                        </div>
                        <div class="mt-4 text-center sm:mt-0 sm:ml-6 sm:text-left w-full">
                            <h3 class="text-xl leading-6 font-bold text-gray-900" id="modal-title">
                                Unlock Candidate
                            </h3>
                            <div class="mt-3">
                                <p class="text-sm text-gray-600 mb-5 leading-relaxed">
                                    You are about to pay <span class="font-bold text-gray-900">{{ unlockCurrency }} {{ unlockPrice }}</span> to unlock candidate contact information.
                                </p>
                                
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">After successful payment you will receive:</p>
                                <ul class="space-y-2.5 mb-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
                                    <li class="flex items-center gap-3 text-sm font-medium text-gray-700">
                                        <CheckCircleIcon class="w-5 h-5 text-green-500" />
                                        <span>Candidate Email Address</span>
                                    </li>
                                    <li class="flex items-center gap-3 text-sm font-medium text-gray-700">
                                        <CheckCircleIcon class="w-5 h-5 text-green-500" />
                                        <span>Candidate Phone Number</span>
                                    </li>
                                    <li class="flex items-center gap-3 text-sm font-medium text-gray-700">
                                        <CheckCircleIcon class="w-5 h-5 text-green-500" />
                                        <span>Resume Download</span>
                                    </li>
                                </ul>

                                <div v-if="error" class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-sm font-medium mb-5">
                                    {{ error }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                        <button type="button" @click="startUnlock" :disabled="loading" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-5 py-3 bg-emerald-600 text-base font-bold text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:w-auto sm:text-sm disabled:opacity-75 transition-colors flex items-center justify-center min-w-[200px]">
                            <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-if="!loading">Continue to Payment</span>
                            <span v-else>Creating Secure Checkout...</span>
                        </button>
                        <button type="button" @click="isModalOpen = false" :disabled="loading" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-5 py-3 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:w-auto sm:text-sm disabled:opacity-50 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
