<?php
    namespace App\Http\Controllers\Backend;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\RoomRequest;
    use App\Services\HouseService;
    use Illuminate\Support\Facades\DB;
    use App\Services\RoomService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Schema;
    use Inertia\Inertia;
    use App\Traits\SystemTrait;
    use Exception;

    class RoomController extends Controller
    {
        use SystemTrait;

        protected $roomService, $houseService;

        public function __construct(RoomService $roomService, HouseService $houseService)
        {
            $this->roomService = $roomService;
            $this->houseService = $houseService;
        }

        public function index()
        {
            return Inertia::render(
                'Backend/Room/Index',
                [
                    'pageTitle' => fn () => 'Room List',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Room Manage'],
                        ['link' => route('backend.room.index'), 'title' => 'Room List'],
                    ],
                    'tableHeaders' => fn () => $this->getTableHeaders(),
                    'dataFields' => fn () => $this->getDataFields(),
                    'datas' => fn () => $this->getDatas(),
                ]
            );
        }

    private function getDataFields()
    {
        return [
            ['fieldName' => 'index', 'class' => 'text-center'],
            ['fieldName' => 'house_id', 'class' => 'text-center'],
			['fieldName' => 'room_number', 'class' => 'text-center'],
			['fieldName' => 'type', 'class' => 'text-center'],
			['fieldName' => 'rent', 'class' => 'text-right'],
			['fieldName' => 'availability', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'House Address',
			'Room Number',
			'Type',
			'Rent Amount',
			'Availability',
            'Status',
            'Action'
        ];
    }

    public function bookingPrice($room_id){
        return $this->roomService->bookingPriceByRoomId($room_id);
    }

    private function getDatas()
    {
        $query = $this->roomService->list();

			if(request()->filled('type'))
				$query->where('type', 'like', '%'. request()->type .'%');

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->house_id = $data->house->address;
			$customData->room_number = $data->room_number;
			$customData->type = $data->type;
			$customData->rent = $data->rent;
			$customData->availability = $data->availability == 1 ? "Yes" : "No";


            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                  [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.room.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.room.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.room.destroy', $data->id),
                    'linkLabel' => getLinkLabel('Delete', null, null)
                ]
            ];
            return $customData;
        });

        return regeneratePagination($formatedDatas, $datas->total(), $datas->perPage(), $datas->currentPage());
    }

        public function create()
        {
            $houseDetails = $this->houseService->activeList();
            return Inertia::render(
                'Backend/Room/Form',
                [
                    'pageTitle' => fn () => 'Room Create',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Room Manage'],
                        ['link' => route('backend.room.create'), 'title' => 'Room Create'],
                    ],
                    "houseDetails" => $houseDetails,
                ]
            );
        }


        public function store(RoomRequest $request)
        {

            DB::beginTransaction();
            try {

                $data = $request->validated();

                $dataInfo = $this->roomService->create($data);

                if ($dataInfo) {
                    $message = 'Room created successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'rooms', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To create Room.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {

                DB::rollBack();
                $this->storeSystemError('Backend', 'RoomController', 'store', substr($err->getMessage(), 0, 1000));

                DB::commit();
                $message = "Server Errors Occur. Please Try Again.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        }

        public function edit($id)
        {
            $room = $this->roomService->find($id);
            $houseDetails = $this->houseService->activeList();

            return Inertia::render(
                'Backend/Room/Form',
                [
                    'pageTitle' => fn () => 'Room Edit',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Room Manage'],
                        ['link' => route('backend.room.edit', $id), 'title' => 'Room Edit'],
                    ],
                    'room' => fn () => $room,
                    'id' => fn () => $id,
                    'houseDetails' => $houseDetails,
                ]
            );
        }

        public function update(RoomRequest $request, $id)
        {
            DB::beginTransaction();
            try {

                $data = $request->validated();
                $Room = $this->roomService->find($id);


                $dataInfo = $this->roomService->update($data, $id);

                if ($dataInfo->save()) {
                    $message = 'Room updated successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'rooms', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To update Room.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Roomcontroller', 'update', substr($err->getMessage(), 0, 1000));
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

                if ($this->roomService->delete($id)) {
                    $message = 'Room deleted successfully';
                    $this->storeAdminWorkLog($id, 'rooms', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To Delete Room.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Roomcontroller', 'destroy', substr($err->getMessage(), 0, 1000));
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
            $dataInfo = $this->roomService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Room ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'rooms', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Room.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'RoomController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors( ['error'=>$message]);
        }
    }
}
