<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\HouseOwnerRequest;
use Illuminate\Support\Facades\DB;
use App\Services\HouseOwnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use App\Traits\SystemTrait;
use Exception;

class HouseOwnerController extends Controller
{
    use SystemTrait;

    protected $houseownerService;

    public function __construct(HouseOwnerService $houseownerService)
    {
        $this->houseownerService = $houseownerService;
    }

    public function index()
    {
        return Inertia::render(
            'Backend/HouseOwner/Index',
            [
                'pageTitle' => fn() => 'HouseOwner List',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'HouseOwner Manage'],
                    ['link' => route('backend.houseowner.index'), 'title' => 'HouseOwner List'],
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
            ['fieldName' => 'name', 'class' => 'text-center'],
            ['fieldName' => 'email', 'class' => 'text-center'],
            ['fieldName' => 'phone', 'class' => 'text-center'],
            ['fieldName' => 'address', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Name',
            'Email',
            'Phone',
            'Address',
            'Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->houseownerService->list();

        if (request()->filled('name'))
            $query->where('name', 'like', '%' . request()->name . '%');

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->name = $data->name ?? '';
            $customData->email = $data->email;
            $customData->phone = $data->phone;
            $customData->address = $data->address;

            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.houseowner.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.houseowner.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.houseowner.destroy', $data->id),
                    'linkLabel' => getLinkLabel('Delete', null, null)
                ]
            ];
            return $customData;
        });

        return regeneratePagination($formatedDatas, $datas->total(), $datas->perPage(), $datas->currentPage());
    }

    public function create()
    {
        return Inertia::render(
            'Backend/HouseOwner/Form',
            [
                'pageTitle' => fn() => 'HouseOwner Create',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'HouseOwner Manage'],
                    ['link' => route('backend.houseowner.create'), 'title' => 'HouseOwner Create'],
                ],
            ]
        );
    }


    public function store(HouseOwnerRequest $request)
    {

        DB::beginTransaction();
        try {

            $data = $request->validated();

            $dataInfo = $this->houseownerService->create($data);

            if ($dataInfo) {
                $message = 'HouseOwner created successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'house_owners', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To create HouseOwner.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {

            DB::rollBack();
            $this->storeSystemError('Backend', 'HouseOwnerController', 'store', substr($err->getMessage(), 0, 1000));

            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";

            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function edit($id)
    {
        $houseowner = $this->houseownerService->find($id);

        return Inertia::render(
            'Backend/HouseOwner/Form',
            [
                'pageTitle' => fn() => 'HouseOwner Edit',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'HouseOwner Manage'],
                    ['link' => route('backend.houseowner.edit', $id), 'title' => 'HouseOwner Edit'],
                ],
                'houseowner' => fn() => $houseowner,
                'id' => fn() => $id,
            ]
        );
    }

    public function update(HouseOwnerRequest $request, $id)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();
            $HouseOwner = $this->houseownerService->find($id);


            $dataInfo = $this->houseownerService->update($data, $id);

            if ($dataInfo->save()) {
                $message = 'HouseOwner updated successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'house_owners', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To update HouseOwner.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'HouseOwnercontroller', 'update', substr($err->getMessage(), 0, 1000));
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

            if ($this->houseownerService->delete($id)) {
                $message = 'HouseOwner deleted successfully';
                $this->storeAdminWorkLog($id, 'house_owners', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To Delete HouseOwner.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'HouseOwnercontroller', 'destroy', substr($err->getMessage(), 0, 1000));
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
            $dataInfo = $this->houseownerService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'HouseOwner ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'house_owners', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " HouseOwner.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'HouseOwnerController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors(['error' => $message]);
        }
    }
}
