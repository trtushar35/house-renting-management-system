<?php
    namespace App\Http\Controllers\Backend;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\FlatRequest;
    use Illuminate\Support\Facades\DB;
    use App\Services\FlatService;
    use App\Services\HouseService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Schema;
    use Inertia\Inertia;
    use App\Traits\SystemTrait;
    use Exception;

    class FlatController extends Controller
    {
        use SystemTrait;

        protected $flatService, $houseService;

        public function __construct(FlatService $flatService, HouseService $houseService)
        {
            $this->flatService = $flatService;
            $this->houseService = $houseService;
        }

        public function index()
        {
            return Inertia::render(
                'Backend/Flat/Index',
                [
                    'pageTitle' => fn () => 'Flat List',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Flat Manage'],
                        ['link' => route('backend.flat.index'), 'title' => 'Flat List'],
                    ],
                    'tableHeaders' => fn () => $this->getTableHeaders(),
                    'dataFields' => fn () => $this->getDataFields(),
                    'datas' => fn () => $this->getDatas(),
                ]
            );
        }

        public function bookingPrice($flat_id){
            return $this->flatService->bookingPriceByFlatId($flat_id);
        }

    private function getDataFields()
    {
        return [
            ['fieldName' => 'index', 'class' => 'text-center'],
            ['fieldName' => 'house_id', 'class' => 'text-center'],
            ['fieldName' => 'house_owner', 'class' => 'text-center'],
			['fieldName' => 'floor_number', 'class' => 'text-center'],
			['fieldName' => 'flat_number', 'class' => 'text-center'],
			['fieldName' => 'num_bedrooms', 'class' => 'text-center'],
			['fieldName' => 'num_bathrooms', 'class' => 'text-center'],
			['fieldName' => 'square_footage', 'class' => 'text-center'],
			['fieldName' => 'rent', 'class' => 'text-right'],
			['fieldName' => 'availability', 'class' => 'text-center'],
			['fieldName' => 'available_date', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'House Address',
            'House Owner Name',
            'Floor Number',
			'Flat Number',
			'Num Bedrooms',
			'Num Bathrooms',
			'Square Footage',
			'Rent Amount',
			'Availability',
			'Available Date',
            'Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->flatService->list();

        if (request()->filled('address'))
            $query->whereHas('house', function ($query) {
                    $query->where('address', 'like', '%' . request()->address . '%');
            });

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->house_id = $data->house->address;
            $customData->house_owner = $data->house->houseOwner->name;
			$customData->floor_number = $data->floor_number;
			$customData->flat_number = $data->flat_number;
			$customData->num_bedrooms = $data->num_bedrooms;
			$customData->num_bathrooms = $data->num_bathrooms;
			$customData->square_footage = $data->flatImages->pluck('square_footage')->implode(', ');
            $customData->rent = $data->rent;
			$customData->availability = $data->availability == 1 ? 'Yes' : 'No';
			$customData->available_date = $data->available_date;

            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                  [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.flat.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.flat.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.flat.destroy', $data->id),
                    'linkLabel' => getLinkLabel('Delete', null, null)
                ]
            ];
            return $customData;
        });

        return regeneratePagination($formatedDatas, $datas->total(), $datas->perPage(), $datas->currentPage());
    }

        public function create()
        {
            $houseDetails =  $this->houseService->activeList();
            return Inertia::render(
                'Backend/Flat/Form',
                [
                    'pageTitle' => fn () => 'Flat Create',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Flat Manage'],
                        ['link' => route('backend.flat.create'), 'title' => 'Flat Create'],
                    ],
                    "houseDetails" => $houseDetails,
                    'address' => Inertia::lazy(fn () => $this->houseService->houseAddressByHouseId(request()->house_id)),
                ]
            );
        }


        public function store(FlatRequest $request)
        {

            DB::beginTransaction();
            try {

                $data = $request->validated();

                $dataInfo = $this->flatService->create($data);

                if ($dataInfo) {
                    $message = 'Flat created successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'flats', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To create Flat.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {

                DB::rollBack();
                $this->storeSystemError('Backend', 'FlatController', 'store', substr($err->getMessage(), 0, 1000));

                DB::commit();
                $message = "Server Errors Occur. Please Try Again.";

                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        }

        public function edit($id)
        {
            $flat = $this->flatService->find($id);
            // dd($flat->square_footage);
            $houseDetails =  $this->houseService->activeList();
            return Inertia::render(
                'Backend/Flat/Form',
                [
                    'pageTitle' => fn () => 'Flat Edit',
                    'breadcrumbs' => fn () => [
                        ['link' => null, 'title' => 'Flat Manage'],
                        ['link' => route('backend.flat.edit', $id), 'title' => 'Flat Edit'],
                    ],
                    'flat' => fn () => $flat,
                    'id' => fn () => $id,
                    'houseDetails' => $houseDetails,
                    // 'images' => Inertia::lazy(fn () => $this->flatService->flatImagesByFlatId(request()->flat_id)),

                ]
            );
        }

        public function update(FlatRequest $request, $id)
        {
            DB::beginTransaction();
            try {

                $data = $request->validated();
                $Flat = $this->flatService->find($id);

                if($request->hasFile('square_footage')) {
                    $images = [];
                    foreach($request->file('square_footage') as $image) {
                        $images[] = $this->imageUpload($image, 'flatImage');
                    }
                    $data["square_footage"] = implode(',',$images);
                }

                $dataInfo = $this->flatService->update($data, $id);

                if ($dataInfo->save()) {
                    $message = 'Flat updated successfully';
                    $this->storeAdminWorkLog($dataInfo->id, 'flats', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To update Flat.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Flatcontroller', 'update', substr($err->getMessage(), 0, 1000));
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

                if ($this->flatService->delete($id)) {
                    $message = 'Flat deleted successfully';
                    $this->storeAdminWorkLog($id, 'flats', $message);

                    DB::commit();

                    return redirect()
                        ->back()
                        ->with('successMessage', $message);
                } else {
                    DB::rollBack();

                    $message = "Failed To Delete Flat.";
                    return redirect()
                        ->back()
                        ->with('errorMessage', $message);
                }
            } catch (Exception $err) {
                DB::rollBack();
                $this->storeSystemError('Backend', 'Flatcontroller', 'destroy', substr($err->getMessage(), 0, 1000));
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
            $dataInfo = $this->flatService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Flat ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'flats', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Flat.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'FlatController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors( ['error'=>$message]);
        }
    }
}
