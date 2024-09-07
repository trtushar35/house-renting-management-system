<script setup>
import { ref, onMounted } from 'vue';
import BackendLayout from '@/Layouts/BackendLayout.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AlertMessage from '@/Components/AlertMessage.vue';
import { displayResponse, displayWarning } from '@/responseMessage.js';

const props = defineProps(["payment", "id", "bookingDetails", "roomDetails"]);

const form = useForm({
    booking_id: props.payment?.booking_id ?? '',
    payment_date: props.payment?.payment_date ?? '',
    payment_month: props.payment?.payment_month ?? '',
    amount: props.payment?.amount ?? '',
    paid_amount: props.payment?.paid_amount ?? '',
    due: props.payment?.due ?? '',
    payment_method: props.payment?.payment_method ?? '',

    imagePreview: props.payment?.image ?? "",
    filePreview: props.payment?.file ?? "",
    _method: props.payment?.id ? "put" : "post",
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

const selectedValue = ref('');

const handleClick = async () => {

    try {
        const response = await axios.get(`/booking/${form.booking_id}/rent`);
        form.amount = response.data.rent; // Update form amount with fetched rent
    } catch (error) {
        console.error('Error fetching rent:', error);
        // Handle error as needed
    }
};

const submit = () => {
    const routeName = props.id
        ? route("backend.payment.update", props.id)
        : route("backend.payment.store");
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


const filterPrice = async (booking_id) => {
    let routeName = props.id ? route('backend.payment.edit', props.id) : route('backend.payment.create');

    try {
        await router.visit(routeName, {
            only: ["amount"],
            data: { booking_id: booking_id },
            preserveState: true,
            onSuccess: (page) => {
                console.log(page.props);
                form.amount = page.props.amount || 0;
            },
        });
    } catch (error) {
        console.error('Error fetching price:', error);
        // Display an error message to the user, if needed
    }
};

const calculateDue = () => {
    if (form.amount && form.paid_amount) {
        form.due = parseFloat(form.amount) - parseFloat(form.paid_amount);
    }
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
                        <InputLabel for="booking_id" value="Booking Tenants Name" />
                        <select id="booking_id" @change="filterPrice(form.booking_id)"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.booking_id" type="text">
                            <option value="">Select Tenants Name</option>
                            <option v-for="bookingDetail in bookingDetails" :key="bookingDetail.id"
                                :value="bookingDetail.id">
                                <template v-if="bookingDetail.tenant">
                                    {{ bookingDetail.tenant.name }} ({{ bookingDetail.tenant.id }})
                                </template>
                                <template v-else-if="bookingDetail.flat.house.house_owner">
                                    {{ bookingDetail.flat.house.house_owner.name }} ({{
                                        bookingDetail.flat.house.house_owner.id }})
                                </template>
                                <template v-else>
                                    N/A (N/A)
                                </template>
                            </option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.booking_id" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="payment_date" value="Payment Date" />
                        <input id="payment_date"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.payment_date" type="date" placeholder="Payment Date" />
                        <InputError class="mt-2" :message="form.errors.payment_date" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="payment_month" value="Payment Month" />
                        <select id="payment_month"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.payment_month">
                            <option value="">Select Payment Month</option>
                            <option value="January">January</option>
                            <option value="February">February</option>
                            <option value="March">March</option>
                            <option value="April">April</option>
                            <option value="May">May</option>
                            <option value="June">June</option>
                            <option value="July">July</option>
                            <option value="August">August</option>
                            <option value="September">September</option>
                            <option value="October">October</option>
                            <option value="November">November</option>
                            <option value="December">December</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.payment_month" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="amount" value="Amount" />
                        <input id="amount"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.amount" type="text" placeholder="Amount" readonly />
                        <InputError class="mt-2" :message="form.errors.amount" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="paid_amount" value="Pay Amount" />
                        <input id="paid_amount" @change="calculateDue()"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.paid_amount" type="number" placeholder="Pay Amount" />
                        <InputError class="mt-2" :message="form.errors.paid_amount" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="due" value="Due" />
                        <input id="due"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.due" type="text" placeholder="Due" readonly />
                        <InputError class="mt-2" :message="form.errors.due" />
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="payment_method" value="Payment method" />
                        <select id="payment_method"
                            class="block w-full p-2 text-sm rounded-md shadow-sm border-slate-300 dark:border-slate-500 dark:bg-slate-700 dark:text-slate-200 focus:border-indigo-300 dark:focus:border-slate-600"
                            v-model="form.payment_method" type="text" placeholder="Payment method">
                            <option value="">Select Payment Method</option>
                            <option value="cod">COD</option>
                            <option value="ssl">SSL</option>
                        </select>
                        <InputError class="mt-2" :message="form.errors.payment_method" />
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
