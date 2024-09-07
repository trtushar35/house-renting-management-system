<script setup>
import { ref, watch } from 'vue';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { router, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AlertMessage from '@/Components/AlertMessage.vue';
import { displayResponse, displayWarning } from '@/responseMessage.js';

const props = defineProps(["booking", "id", "flatDetails", "roomDetails", "teantDetails", "houseDetails"]);

const form = useForm({
    flat_id: props.booking?.flat_id ?? '',
    house_id: props.booking?.house_id ?? '',
    room_id: props.booking?.room_id ?? '',
    tenant_id: props.booking?.tenant_id ?? '',
    rent: props.booking?.rent ?? '',
    booking_status: props.booking?.booking_status ?? '',
    imagePreview: props.booking?.image ?? '',
    filePreview: props.booking?.file ?? '',
    _method: props.booking?.id ? 'put' : 'post',
});

const flatAmount = ref(0);
const roomAmount = ref(0);

const fetchFlatAmount = async (flat_id) => {
    if (!flat_id) {
        flatAmount.value = 0;
        updateTotalRent();
        return;
    }

    let routeName = props.id ? route('backend.booking.edit', props.id) : route('backend.booking.create');

    try {
        await router.visit(routeName, {
            only: ["amount"],
            data: { flat_id: flat_id },
            preserveState: true,
            onSuccess: (page) => {
                console.log('Flat amount fetched:', page.props.amount);
                flatAmount.value = page.props.amount || 0;
                updateTotalRent();
            },
        });
    } catch (error) {
        console.error('Error fetching flat amount:', error);
        flatAmount.value = 0; // Reset flat amount on error
        updateTotalRent();
    }
};

const fetchRoomAmount = async (room_id) => {
    if (!room_id) {
        roomAmount.value = 0;
        updateTotalRent();
        return;
    }

    let routeName = props.id ? route('backend.booking.edit', props.id) : route('backend.booking.create');

    try {
        await router.visit(routeName, {
            only: ["roomAmount"],
            data: { room_id: room_id },
            preserveState: true,
            onSuccess: (page) => {
                console.log('Room amount fetched:', page.props.roomAmount);
                roomAmount.value = page.props.roomAmount || 0;
                updateTotalRent();
            },
        });
    } catch (error) {
        console.error('Error fetching room amount:', error);
        roomAmount.value = 0; // Reset room amount on error
        updateTotalRent();
    }
};

const updateTotalRent = () => {
    form.rent = (parseFloat(flatAmount.value) || 0) + (parseFloat(roomAmount.value) || 0);
    console.log('Total rent updated:', form.rent);
};

watch(() => form.flat_id, (newFlatId) => {
    fetchFlatAmount(newFlatId);
});

watch(() => form.room_id, (newRoomId) => {
    fetchRoomAmount(newRoomId);
});

const flatOptions = ref([]);

const fetchFlatOptions = (house_id) => {
    if (!house_id) {
        flatOptions.value = [];
        return;
    }

    // Simulate fetching flat options based on house_id
    flatOptions.value = props.flatDetails.filter(flat => flat.house_id === house_id);
};

watch(() => form.house_id, (newHouseId) => {
    fetchFlatOptions(newHouseId);
});

const submit = () => {
    const routeName = props.id ? route('backend.booking.update', props.id) : route('backend.booking.store');
    form
        .transform((data) => ({
            ...data,
            rent: form.rent,
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
            class="w-full mt-3 transition duration-1000 ease-in-out transform bg-white border border-gray-700 rounded-md shadow-lg shadow-gray-800/50">
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
                        <select id="house_id"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.house_id">
                            <option value="">Select House Number</option>
                            <option v-for="houseNumber in houseDetails" :key="houseNumber.id" :value="houseNumber.id">
                                {{ houseNumber.house_name }} ({{ houseNumber.house_number }})
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.house_id" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="flat_id" value="Flat Number" />
                        <select id="flat_id"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.flat_id">
                            <option value="">Select Flat Number</option>
                            <option v-for="flatDetail in flatOptions" :key="flatDetail.id" :value="flatDetail.id">
                                {{ flatDetail.flat_number }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.flat_id" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="tenant_id" value="Tenant Name" />
                        <select id="tenant_id"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.tenant_id">
                            <option value="">Select Tenant Name</option>
                            <option v-for="tenantDetail in teantDetails" :key="tenantDetail.id"
                                :value="tenantDetail.id">
                                {{ tenantDetail.name }}
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.tenant_id" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="rent" value="Total Rent" />
                        <input id="rent"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.rent" type="number" placeholder="Total Rent" />
                        <InputError class="mt-2" :message="form.errors.rent" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="booking_status" value="Booking Status" />
                        <select id="booking_status"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.booking_status">
                            <option value="">Select Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Rejected">Rejected</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.booking_status" />
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
