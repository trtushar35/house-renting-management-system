<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Services\BookingService;
use Illuminate\Support\Facades\DB;
use App\Services\PaymentService;
use App\Services\RoomService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use App\Traits\SystemTrait;
use Exception;

class PaymentController extends Controller
{
    use SystemTrait;

    protected $paymentService, $bookingService, $roomService;

    public function __construct(PaymentService $paymentService, BookingService $bookingService, RoomService $roomService)
    {
        $this->paymentService = $paymentService;
        $this->bookingService = $bookingService;
        $this->roomService = $roomService;
    }

    public function index()
    {
        return Inertia::render(
            'Backend/Payment/Index',
            [
                'pageTitle' => fn() => 'Payment List',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Payment Manage'],
                    ['link' => route('backend.payment.index'), 'title' => 'Payment List'],
                ],
                'tableHeaders' => fn() => $this->getTableHeaders(),
                'dataFields' => fn() => $this->getDataFields(),
                'datas' => fn() => $this->getDatas(),
            ]
        );
    }

    private function getDataFields()
    {
        return [
            ['fieldName' => 'index', 'class' => 'text-center'],
            ['fieldName' => 'booking_id', 'class' => 'text-center'],
            ['fieldName' => 'payment_date', 'class' => 'text-center'],
            ['fieldName' => 'payment_month', 'class' => 'text-center'],
            ['fieldName' => 'amount', 'class' => 'text-right'],
            ['fieldName' => 'paid_amount', 'class' => 'text-right'],
            ['fieldName' => 'due', 'class' => 'text-right'],
            ['fieldName' => 'payment_method', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Booking Tenants Name',
            'Payment Date',
            'Payment Month',
            'Amount',
            'Paid Amount',
            'Due',
            'Payment Method',
            'Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->paymentService->list();


        if (request()->filled('name'))
            $query->whereHas('booking', function ($query) {
                $query->whereHas('tenant', function ($query) {
                    $query->where('name', 'like', '%' . request()->name . '%');
                });
            });

        if (request()->filled('payment_date'))
            $query->where('payment_date', 'like', request()->payment_date . '%');

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->booking_id = $data->booking->tenant->name ?? $data->booking->flat->flat_number;
            $customData->payment_date = $data->payment_date;
            $customData->payment_month = $data->payment_month;
            $customData->amount = $data->amount;
            $customData->paid_amount = $data->paid_amount;
            $customData->rent = $data->rent;
            $customData->due = $data->due;
            $customData->payment_method = $data->payment_method;


            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.payment.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.payment.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.payment.destroy', $data->id),
                    'linkLabel' => getLinkLabel('Delete', null, null)
                ]
            ];
            return $customData;
        });

        return regeneratePagination($formatedDatas, $datas->total(), $datas->perPage(), $datas->currentPage());
    }

    public function create()
    {
        $bookingDetails = $this->bookingService->activeList();
        $roomDetails = $this->roomService->activeList();

        return Inertia::render(
            'Backend/Payment/Form',
            [
                'pageTitle' => fn() => 'Payment Create',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Payment Manage'],
                    ['link' => route('backend.payment.create'), 'title' => 'Payment Create'],
                ],
                'bookingDetails' => $bookingDetails,
                'roomDetails' => $roomDetails,
                'amount' => Inertia::lazy(fn() => $this->bookingService->bookingPriceByBookingId(request()->booking_id))
            ]
        );
    }


    public function store(PaymentRequest $request)
    {

        DB::beginTransaction();
        try {

            $data = $request->validated();

            $dataInfo = $this->paymentService->create($data);

            if ($dataInfo) {
                $message = 'Payment created successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'payments', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To create Payment.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {

            DB::rollBack();
            $this->storeSystemError('Backend', 'PaymentController', 'store', substr($err->getMessage(), 0, 1000));

            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";

            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function edit($id)
    {
        $payment = $this->paymentService->find($id);
        $bookingDetails = $this->bookingService->activeList();

        return Inertia::render(
            'Backend/Payment/Form',
            [
                'pageTitle' => fn() => 'Payment Edit',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Payment Manage'],
                    ['link' => route('backend.payment.edit', $id), 'title' => 'Payment Edit'],
                ],
                'payment' => fn() => $payment,
                'id' => fn() => $id,
                'bookingDetails' => $bookingDetails,
                'amount' => Inertia::lazy(fn() => $this->bookingService->bookingPriceByBookingId(request()->booking_id))
            ]
        );
    }

    public function update(PaymentRequest $request, $id)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();
            $Payment = $this->paymentService->find($id);


            $dataInfo = $this->paymentService->update($data, $id);

            if ($dataInfo->save()) {
                $message = 'Payment updated successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'payments', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To update Payment.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'Paymentcontroller', 'update', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function destroy($id)
    {

        DB::beginTransaction();

        try {

            if ($this->paymentService->delete($id)) {
                $message = 'Payment deleted successfully';
                $this->storeAdminWorkLog($id, 'payments', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To Delete Payment.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'Paymentcontroller', 'destroy', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function changeStatus()
    {
        DB::beginTransaction();

        try {
            $dataInfo = $this->paymentService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Payment ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'payments', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Payment.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'PaymentController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors(['error' => $message]);
        }
    }
}
