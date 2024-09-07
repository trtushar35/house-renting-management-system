<script setup>
import { ref, onMounted } from 'vue';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AlertMessage from '@/Components/AlertMessage.vue';
import { displayResponse, displayWarning } from '@/responseMessage.js';

const props = defineProps(["room", "id", "houseDetails"]);

const form = useForm({
    house_id: props.room?.house_id ?? '',
    room_number: props.room?.room_number ?? '',
    type: props.room?.type ?? '',
    rent: props.room?.rent ?? '',
    availability: props.room?.availability ?? '',

    imagePreview: props.room?.image ?? "",
    filePreview: props.room?.file ?? "",
    _method: props.room?.id ? "put" : "post",
});

const handleimageChange = (event) => {
    const file = event.target.files[0];
    form.image = file;

    // Display image preview
    const reader = new FileReader();
    reader.onload = (e) => {
        form.imagePreview = e.target.result;
    };
    reader.readAsDataURL(file);
};

const handlefileChange = (event) => {
    const file = event.target.files[0];
    form.file = file;
};

const submit = () => {
    const routeName = props.id
        ? route("backend.room.update", props.id)
        : route("backend.room.store");
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
                        <InputLabel for="house_id" value="House Address" />
                        <select id="house_id"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.house_id" type="number" placeholder="House Address">
                            <option value="">Select House Address</option>
                            <option v-for="houseDetail in houseDetails" :key="houseDetail.id" :value="houseDetail.id">
                                {{ houseDetail.address }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.house_id" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="room_number" value="Room Number" />
                        <input id="room_number"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.room_number" type="text" placeholder="Room Number" />
                        <InputError class="mt-2" :message="form.errors.room_number" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="type" value="Type" />
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" id="type_single" name="type" value="single" v-model="form.type"
                                    class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                                <span class="ml-2">Single</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" id="type_double" name="type" value="double" v-model="form.type"
                                    class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                                <span class="ml-2">Double</span>
                            </label>
                        </div>
                        <InputError class="mt-2" :message="form.errors.type" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="rent" value="Rent" />
                        <input id="rent"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.rent" type="text" placeholder="Rent" />
                        <InputError class="mt-2" :message="form.errors.rent" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="availability" value="Availability" />
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" id="availability_yes" name="availability" value="1"
                                    v-model="form.availability"
                                    class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" id="availability_no" name="availability" value="0"
                                    v-model="form.availability"
                                    class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                        <InputError class="mt-2" :message="form.errors.availability" />
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
