<script setup>
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AlertMessage from '@/Components/AlertMessage.vue';
import { displayResponse, displayWarning } from '@/responseMessage.js';

const props = defineProps(["house", "id", "houseOwners"]);

const form = useForm({
    house_owner_id: props.house?.house_owner_id ?? '',
    house_name: props.house?.house_name ?? '',
    house_number: props.house?.house_number ?? '',
    address: props.house?.address ?? '',
    division: props.house?.division ?? '',
    city: props.house?.city ?? '',
    country: props.house?.country ?? '',

    imagePreview: props.house?.image ?? "",
    filePreview: props.house?.file ?? "",
    _method: props.house?.id ? "put" : "post",
});

const submit = () => {
    const routeName = props.id
        ? route("backend.house.update", props.id)
        : route("backend.house.store");
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
                        <InputLabel for="house_owner_name" value="House Owner Name" />
                        <select id="house_owner_name"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.house_owner_id" type="text">
                            <option value="">Select House Owner Name</option>
                            <option v-for="houseOwner in houseOwners" :key="houseOwner.id" :value="houseOwner.id">
                                {{ houseOwner.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.house_owner_id" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="house_name" value="House Name" />
                        <input id="house_name"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.house_name" type="text" placeholder="House Name" />
                        <InputError class="mt-2" :message="form.errors.house_name" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="house_number" value="House Number" />
                        <input id="house_number"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.house_number" type="number" placeholder="House Number" />
                        <InputError class="mt-2" :message="form.errors.house_number" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="address" value="Address" />
                        <input id="address"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.address" type="text" placeholder="Address" />
                        <InputError class="mt-2" :message="form.errors.address" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="division" value="Division" />
                        <select id="division"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.division" type="text" placeholder="Division">
                            <option value="">Select Division</option>
                            <option value="dhaka">Dhaka</option>
                            <option value="mymensingh">Mymensingh</option>
                            <option value="khulna">Khulna</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.division" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="city" value="City" />
                        <input id="city"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.city" type="text" placeholder="City" />
                        <InputError class="mt-2" :message="form.errors.city" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="country" value="Country" />
                        <input id="country"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.country" type="text" placeholder="Country" />
                        <InputError class="mt-2" :message="form.errors.country" />
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
