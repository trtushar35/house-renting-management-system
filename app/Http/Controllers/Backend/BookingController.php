<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Services\HouseService;
use Illuminate\Support\Facades\DB;
use App\Services\BookingService;
use App\Services\FlatService;
use App\Services\RoomService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use App\Traits\SystemTrait;
use Exception;

class BookingController extends Controller
{
    use SystemTrait;

    protected $bookingService, $flatService, $roomService, $tenantService, $houseService;

    public function __construct(BookingService $bookingService, HouseService $houseService, FlatService $flatService, RoomService $roomService, TenantService $tenantService)
    {
        $this->bookingService = $bookingService;
        $this->flatService = $flatService;
        $this->roomService = $roomService;
        $this->tenantService = $tenantService;
        $this->houseService = $houseService;
    }

    public function index()
    {
        return Inertia::render(
            'Backend/Booking/Index',
            [
                'pageTitle' => fn () => 'Booking List',
                'breadcrumbs' => fn () => [
                    ['link' => null, 'title' => 'Booking Manage'],
                    ['link' => route('backend.booking.index'), 'title' => 'Booking List'],
                ],
                'tableHeaders' => fn () => $this->getTableHeaders(),
                'dataFields' => fn () => $this->getDataFields(),
                'datas' => fn () => $this->getDatas(),
            ]
        );
    }

    public function bookingPrice($booking_id)
    {
        return $this->bookingService->bookingPriceByBookingId($booking_id);
    }

    private function getDataFields()
    {
        return [
            ['fieldName' => 'index', 'class' => 'text-center'],
            ['fieldName' => 'tenant_id', 'class' => 'text-center'],
            ['fieldName' => 'house_number', 'class' => 'text-center'],
            ['fieldName' => 'flat_id', 'class' => 'text-center'],
            ['fieldName' => 'total_bedrooms', 'class' => 'text-center'],
            ['fieldName' => 'total_bathrooms', 'class' => 'text-center'],
            ['fieldName' => 'rent', 'class' => 'text-right'],
            ['fieldName' => 'rent_amount', 'class' => 'text-right'],
            ['fieldName' => 'booking_status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Tenant Name',
            'House Number',
            'Flat Number',
            'Total Bedrooms',
            'Total Bathrooms',
            'Total Rent',
            'Rent Amount',
            'Booking Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->bookingService->list();

        if (request()->filled('name'))
            $query->whereHas('tenant', function ($query) {
                $query->where('name', 'like', '%' . request()->name . '%');
            });

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->flat_id = $data->flat ? $data->flat->flat_number ?? '' : '';
            $customData->tenant_id = $data->tenant->name;
            $customData->house_number =  $data->flat->house->house_number;
            $customData->total_bedrooms = $data->flat ? $data->flat->num_bedrooms ?? '' : '';
            $customData->total_bathrooms = $data->flat ? $data->flat->num_bathrooms ?? '' : '';
            $customData->rent = $data->rent;
            $customData->rent_amount = $data->flat->rent;
            $customData->booking_status = $data->booking_status;

            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.booking.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.booking.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.booking.destroy', $data->id),
                    'linkLabel' => getLinkLabel('Delete', null, null)
                ]
            ];
            return $customData;
        });

        return regeneratePagination($formatedDatas, $datas->total(), $datas->perPage(), $datas->currentPage());
    }

    public function create()
    {
        $flatDetails = $this->flatService->availableFlatList();
        $roomDetails = $this->roomService->activeList();
        $tenantDetails = $this->tenantService->activeList();
        $houseDetails = $this->houseService->activeList();
        return Inertia::render(
            'Backend/Booking/Form',
            [
                'pageTitle' => fn () => 'Booking Create',
                'breadcrumbs' => fn () => [
                    ['link' => null, 'title' => 'Booking Manage'],
                    ['link' => route('backend.booking.create'), 'title' => 'Booking Create'],
                ],
                'flatDetails' => $flatDetails,
                'roomDetails' => $roomDetails,
                'teantDetails' => $tenantDetails,
                'houseDetails' => $houseDetails,
                'amount' => Inertia::lazy(fn () => $this->flatService->bookingPriceByFlatId(request()->flat_id)),
                'roomAmount' => Inertia::lazy(fn () => $this->roomService->bookingPriceByRoomId(request()->room_id))
            ]
        );
    }


    public function store(BookingRequest $request)
    {

        DB::beginTransaction();
        try {

            $data = $request->validated();
            // dd($data);

            $dataInfo = $this->bookingService->create($data);

            if (isset($data['flat_id']) && $flat = $this->flatService->find($data['flat_id'])) {
                $flat->update(['availability' => $data['booking_status'] == 'Approved' ? 0 : 1]);
            }

            if ($dataInfo) {
                $message = 'Booking created successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'bookings', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To create Booking.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {

            DB::rollBack();
            $this->storeSystemError('Backend', 'BookingController', 'store', substr($err->getMessage(), 0, 1000));

            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";

            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function edit($id)
    {
        $booking = $this->bookingService->find($id);
        $flatDetails = $this->flatService->availableFlatList();
        $roomDetails = $this->roomService->activeList();
        $tenantDetails = $this->tenantService->activeList();

        return Inertia::render(
            'Backend/Booking/Form',
            [
                'pageTitle' => fn () => 'Booking Edit',
                'breadcrumbs' => fn () => [
                    ['link' => null, 'title' => 'Booking Manage'],
                    ['link' => route('backend.booking.edit', $id), 'title' => 'Booking Edit'],
                ],
                'booking' => fn () => $booking,
                'id' => fn () => $id,
                'flatDetails' => $flatDetails,
                'roomDetails' => $roomDetails,
                'teantDetails' => $tenantDetails,
                'amount' => Inertia::lazy(fn () => $this->flatService->bookingPriceByFlatId(request()->flat_id))
            ]
        );
    }

    public function update(BookingRequest $request, $id)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();
            $Booking = $this->bookingService->find($id);
            $dataInfo = $this->bookingService->update($data, $id);

            if ($dataInfo->save()) {
                $message = 'Booking updated successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'bookings', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To update Booking.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'Bookingcontroller', 'update', substr($err->getMessage(), 0, 1000));
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

            if ($this->bookingService->delete($id)) {
                $message = 'Booking deleted successfully';
                $this->storeAdminWorkLog($id, 'bookings', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To Delete Booking.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'Bookingcontroller', 'destroy', substr($err->getMessage(), 0, 1000));
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
            $dataInfo = $this->bookingService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Booking ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'bookings', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Booking.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'BookingController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors(['error' => $message]);
        }
    }
}
