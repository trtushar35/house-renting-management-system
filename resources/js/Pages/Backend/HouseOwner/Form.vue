<script setup>
import { ref, onMounted } from 'vue';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AlertMessage from '@/Components/AlertMessage.vue';
import { displayResponse, displayWarning } from '@/responseMessage.js';

const props = defineProps(["houseowner", "id"]);

const form = useForm({
    name: props.houseowner?.name ?? '',
    email: props.houseowner?.email ?? '',
    phone: props.houseowner?.phone ?? '',
    address: props.houseowner?.address ?? '',
    password: props.houseowner?.password ?? '',

    imagePreview: props.houseowner?.image ?? "",
    filePreview: props.houseowner?.file ?? "",
    _method: props.houseowner?.id ? "put" : "post",
});

const submit = () => {
    const routeName = props.id
        ? route("backend.houseowner.update", props.id)
        : route("backend.houseowner.store");
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
                        <InputLabel for="name" value="Name" />
                        <input id="name"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.name" type="text" placeholder="Name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="email" value="Email" />
                        <input id="email"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.email" type="text" placeholder="Email" />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="phone" value="Phone" />
                        <input id="phone"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.phone" type="text" placeholder="Phone" />
                        <InputError class="mt-2" :message="form.errors.phone" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="address" value="Address" />
                        <input id="address"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.address" type="text" placeholder="Address" />
                        <InputError class="mt-2" :message="form.errors.address" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="password" value="Password" />
                        <input id="password"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.password" type="password" placeholder="Password" />
                        <InputError class="mt-2" :message="form.errors.password" />
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
