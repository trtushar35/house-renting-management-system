<script setup>
import { ref, onMounted } from 'vue';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AlertMessage from '@/Components/AlertMessage.vue';
import { displayResponse, displayWarning } from '@/responseMessage.js';

const props = defineProps(["flat", "id", "houseDetails", "images"]);

const form = useForm({
    house_id: props.flat?.house_id ?? '',
    address: props.flat?.address ?? '',
    floor_number: props.flat?.floor_number ?? '',
    flat_number: props.flat?.flat_number ?? '',
    num_bedrooms: props.flat?.num_bedrooms ?? '',
    num_bathrooms: props.flat?.num_bathrooms ?? '',
    square_footage: [],
    rent: props.flat?.rent ?? '',
    availability: props.flat?.availability ?? '',
    available_date: props.flat?.available_date ?? '',

    imagePreview: [],
    filePreview: props.flat?.file ?? "",
    _method: props.flat?.id ? "put" : "post",
});

const fetchHouseAddress = async (house_id) => {
    let routeName = props.id ? route('backend.flat.edit', props.id) : route('backend.flat.create');

    try {
        await router.visit(routeName, {
            only: ["address"],
            data: { house_id: house_id },
            preserveState: true,
            onSuccess: (page) => {
                console.log('House address fetched:', page.props.address);
                form.address = page.props.address;
            },
        });
    } catch (error) {
        console.error('Error fetching address:', error);
    }
};

const submit = () => {
    const routeName = props.id
        ? route("backend.flat.update", props.id)
        : route("backend.flat.store");

    const formData = new FormData();
    for (let key in form) {
        if (key === 'square_footage') {
            for (let i = 0; i < form[key].length; i++) {
                formData.append('square_footage[]', form[key][i]);
            }
        } else if (key === 'imagePreview') {
            continue;
        } else {
            formData.append(key, form[key]);
        }
    }
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
                        <InputLabel for="house_id" value="House Number" />
                        <select id="house_id" @change="fetchHouseAddress(form.house_id)"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.house_id" type="text">
                            <option value="">Select House Number</option>
                            <option v-for="houseDetail in houseDetails" :key="houseDetail.id" :value="houseDetail.id">
                                {{ houseDetail.house_name }} ({{ houseDetail.house_number }})
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.house_id" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="address" value="House Address" />
                        <input id="address"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.address" type="text" readonly />
                        <InputError class="mt-2" :message="form.errors.address" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="floor_number" value="Floor Number" />
                        <input id="floor_number"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.floor_number" type="text" placeholder="Floor Number" />
                        <InputError class="mt-2" :message="form.errors.floor_number" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="flat_number" value="Flat Number" />
                        <input id="flat_number"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.flat_number" type="text" placeholder="Flat Number" />
                        <InputError class="mt-2" :message="form.errors.flat_number" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="num_bedrooms" value="Num Bedrooms" />
                        <input id="num_bedrooms"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.num_bedrooms" type="number" placeholder="Num Bedrooms" />
                        <InputError class="mt-2" :message="form.errors.num_bedrooms" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="num_bathrooms" value="Num Bathrooms" />
                        <input id="num_bathrooms"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.num_bathrooms" type="number" placeholder="Num Bathrooms" />
                        <InputError class="mt-2" :message="form.errors.num_bathrooms" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="rent" value="Rent" />
                        <input id="rent"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.rent" type="number" placeholder="Rent" />
                        <InputError class="mt-2" :message="form.errors.rent" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="availability" value="Availability" />

                        <div class="flex items-center mt-1">
                            <input id="availability_yes" v-model="form.availability" type="radio" value="1"
                                class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                            <label for="availability_yes" class="ml-2 block text-sm leading-5 text-gray-900">
                                Yes
                            </label>
                        </div>

                        <div class="flex items-center mt-1">
                            <input id="availability_no" v-model="form.availability" type="radio" value="0"
                                class="form-radio h-4 w-4 text-indigo-600 transition duration-150 ease-in-out">
                            <label for="availability_no" class="ml-2 block text-sm leading-5 text-gray-900">
                                No
                            </label>
                        </div>
                        <InputError class="mt-2" :message="form.errors.availability" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="available_date" value="Available Date" />
                        <input id="available_date"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.available_date" type="date" placeholder="Available Date" />
                        <InputError class="mt-2" :message="form.errors.available_date" />
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
