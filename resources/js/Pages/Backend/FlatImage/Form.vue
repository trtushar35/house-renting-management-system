<script setup>
import { ref, onMounted } from 'vue';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AlertMessage from '@/Components/AlertMessage.vue';
import { displayResponse, displayWarning } from '@/responseMessage.js';

const props = defineProps(["flatimage", "id", "flatDetails"]);

const form = useForm({
    flat_id: props.flatimage?.flat_id ?? '',
    square_footage: [],

    imagePreview: [],
    filePreview: props.flatimage?.file ?? "",
    _method: props.flatimage?.id ? "put" : "post",
});

const handlefileChange = (event) => {
    const files = event.target.files;
    form.square_footage = Array.from(files);

    // Display photo preview
    form.imagePreview = [];
    for (let i = 0; i < files.length; i++) {
        const reader = new FileReader();
        reader.onload = (e) => {
            form.imagePreview.push(e.target.result);
        };
        reader.readAsDataURL(files[i]);

    }
};

const submit = () => {
    const routeName = props.id
        ? route("backend.flatimage.update", props.id)
        : route("backend.flatimage.store");
    form
        .transform((data) => ({
            ...data,
            remember: "",
            isDirty: false,
        }))
        .post(routeName, {
            onSuccess: (response) => {
                if (!props.id) form.reset();
                displayResponse(response);
            },
            onError: (errorObject) => {
                displayWarning(errorObject);
            },
        });
};
</script>

<template>
    <BackendLayout>
        <div
            class="w-full mt-3 transition duration-1000 ease-in-out transform bg-white border border-gray-700 rounded-md shadow-lg shadow-gray-800/50 dark:bg-slate-900">

            <div
                class="flex items-center justify-between w-full text-gray-700 bg-gray-100 rounded-md shadow-md dark:bg-gray-800 dark:text-gray-200 shadow-gray-800/50">
                <div>
                    <h1 class="p-4 text-xl font-bold dark:text-white">{{ $page.props.pageTitle }}</h1>
                </div>
                <div class="p-4 py-2">
                </div>
            </div>

            <form @submit.prevent="submit" class="p-4">
                <AlertMessage />
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4">
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="flat_id" value="Flat Number" />
                        <select id="flat_id"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.flat_id" type="number" placeholder="Flat id">
                            <option value="">Select Flat Number</option>
                            <option v-for="flatDetail in flatDetails" :key="flatDetail.id" :value="flatDetail.id">
                                {{ flatDetail.flat_number }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.flat_id" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="square_footage" value="Square Footage" />
                        <div v-if="form.imagePreview.length > 0">
                            <div v-for="(image, index) in form.imagePreview" :key="index">
                                <img :src="image" alt="Photo Preview" class="max-w-xs mt-2" height="60" width="60" />
                            </div>
                        </div>
                        <input id="square_footage" accept="image/*" multiple @change="handlefileChange" type="file"
                            placeholder="Square footage" />
                        <InputError class="mt-2" :message="form.errors.square_footage" />
                    </div>
                </div>
                <div class="flex items-center justify-end mt-4">
                    <PrimaryButton type="submit" class="ms-4" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        {{ ((props.id ?? false) ? 'Update' : 'Create') }}
                    </PrimaryButton>
                </div>
            </form>

        </div>
    </BackendLayout>
</template>
