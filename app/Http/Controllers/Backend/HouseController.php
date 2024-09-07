<?php
    namespace App\Http\Controllers\Backend;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\HouseRequest;
    use App\Models\HouseOwner;
    use App\Services\HouseOwnerService;
    use Illuminate\Support\Facades\DB;
    use App\Services\HouseService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Schema;
    use Inertia\Inertia;
    use App\Traits\SystemTrait;
    use Exception;

    class HouseController extends Controller
    {
        use SystemTrait;

        protected $houseService, $houseOwnerService;

        public function __construct(HouseService $houseService, HouseOwnerService $houseOwnerService)
        {
            $this->houseService = $houseService;
            $this->houseOwnerService = $houseOwnerService;
        }

        public function index()
        {
            return Inertia::render(
                'Backend/House/Index',
                [
                    'pageTitle' => fn () => 'House List',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'House Manage'],
                        ['link' => route('backend.house.index'), 'title' => 'House List'],
                    ],
                    'tableHeaders' => fn () => $this->getTableHeaders(),
                    'dataFields' => fn () => $this->getDataFields(),
                    'datas' => fn () => $this->getDatas(),
                ]
            );
        }

        public function houseAddress($booking_id)
    {
        return $this->houseService->houseAddressByHouseId($booking_id);
    }

    private function getDataFields()
    {
        return [
            ['fieldName' => 'index', 'class' => 'text-center'],
            ['fieldName' => 'id', 'class' => 'text-center'],
            ['fieldName' => 'house_owner_id', 'class' => 'text-center'],
            ['fieldName' => 'house_name', 'class' => 'text-center'],
            ['fieldName' => 'house_number', 'class' => 'text-center'],
			['fieldName' => 'address', 'class' => 'text-center'],
			['fieldName' => 'division', 'class' => 'text-center'],
			['fieldName' => 'city', 'class' => 'text-center'],
			['fieldName' => 'country', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'House Owner Id',
            'House Owner Name',
            'House Name',
            'House Number',
			'Address',
			'Division',
			'City',
			'Country',
            'Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->houseService->list();

			if(request()->filled('address'))
				$query->where('address', 'like', '%' . request()->address .'%');


        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->id = $data->house_owner_id;
            $customData->house_owner_id = $data->houseOwner->name;
            $customData->house_name = $data->house_name;
            $customData->house_number = $data->house_number;
			$customData->address = $data->address;
			$customData->division = $data->division;
			$customData->city = $data->city;
			$customData->country = $data->country;

            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                  [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.house.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.house.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.house.destroy', $data->id),
                    'linkLabel' => getLinkLabel('Delete', null, null)
                ]
            ];
            return $customData;
        });

        return regeneratePagination($formatedDatas, $datas->total(), $datas->perPage(), $datas->currentPage());
    }

        public function create()
        {
            $houseOwners = $this->houseOwnerService->activeList();
            return Inertia::render(
                'Backend/House/Form',
                [
                    'pageTitle' => fn () => 'House Create',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'House Manage'],
                        ['link' => route('backend.house.create'), 'title' => 'House Create'],
                    ],
                    'houseOwners'=> $houseOwners,
                ]
            );
        }


        public function store(HouseRequest $request)
        {

            DB::beginTransaction();
            try {

                $data = $request->validated();

                $dataInfo = $this->houseService->create($data);

                if ($dataInfo) {
                    $message = 'House created successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'houses', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To create House.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {

                DB::rollBack();
                $this->storeSystemError('Backend', 'HouseController', 'store', substr($err->getMessage(), 0, 1000));

                DB::commit();
                $message = "Server Errors Occur. Please Try Again.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        }

        public function edit($id)
        {
            $house = $this->houseService->find($id);
            $houseOwners = $this->houseOwnerService->activeList();

            return Inertia::render(
                'Backend/House/Form',
                [
                    'pageTitle' => fn () => 'House Edit',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'House Manage'],
                        ['link' => route('backend.house.edit', $id), 'title' => 'House Edit'],
                    ],
                    'house' => fn () => $house,
                    'id' => fn () => $id,
                    'houseOwners'=> $houseOwners,
                ]
            );
        }

        public function update(HouseRequest $request, $id)
        {
            DB::beginTransaction();
            try {

                $data = $request->validated();
                $House = $this->houseService->find($id);


                $dataInfo = $this->houseService->update($data, $id);

                if ($dataInfo->save()) {
                    $message = 'House updated successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'houses', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To update House.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Housecontroller', 'update', substr($err->getMessage(), 0, 1000));
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

                if ($this->houseService->delete($id)) {
                    $message = 'House deleted successfully';
                    $this->storeAdminWorkLog($id, 'houses', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To Delete House.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Housecontroller', 'destroy', substr($err->getMessage(), 0, 1000));
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
            $dataInfo = $this->houseService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'House ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'houses', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " House.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'HouseController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors( ['error'=>$message]);
        }
    }
}
