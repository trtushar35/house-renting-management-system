<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\TenantRequest;
use Illuminate\Support\Facades\DB;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use App\Traits\SystemTrait;
use Exception;

class TenantController extends Controller
{
    use SystemTrait;

    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function index()
    {
        return Inertia::render(
            'Backend/Tenant/Index',
            [
                'pageTitle' => fn() => 'Tenant List',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Tenant Manage'],
                    ['link' => route('backend.tenant.index'), 'title' => 'Tenant List'],
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
        $query = $this->tenantService->list();

        if (request()->filled('name'))
            $query->where('name', 'like', '%' . request()->name . '%');

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->name = $data->name;
            $customData->email = $data->email;
            $customData->phone = $data->phone;
            $customData->address = $data->address;

            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.tenant.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.tenant.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.tenant.destroy', $data->id),
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
            'Backend/Tenant/Form',
            [
                'pageTitle' => fn() => 'Tenant Create',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Tenant Manage'],
                    ['link' => route('backend.tenant.create'), 'title' => 'Tenant Create'],
                ],
            ]
        );
    }


    public function store(TenantRequest $request)
    {

        DB::beginTransaction();
        try {

            $data = $request->validated();

            $dataInfo = $this->tenantService->create($data);

            if ($dataInfo) {
                $message = 'Tenant created successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'tenants', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To create Tenant.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {

            DB::rollBack();
            $this->storeSystemError('Backend', 'TenantController', 'store', substr($err->getMessage(), 0, 1000));

            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";

            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function edit($id)
    {
        $tenant = $this->tenantService->find($id);

        return Inertia::render(
            'Backend/Tenant/Form',
            [
                'pageTitle' => fn() => 'Tenant Edit',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'Tenant Manage'],
                    ['link' => route('backend.tenant.edit', $id), 'title' => 'Tenant Edit'],
                ],
                'tenant' => fn() => $tenant,
                'id' => fn() => $id,
            ]
        );
    }

    public function update(TenantRequest $request, $id)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();
            $Tenant = $this->tenantService->find($id);


            $dataInfo = $this->tenantService->update($data, $id);

            if ($dataInfo->save()) {
                $message = 'Tenant updated successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'tenants', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To update Tenant.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'Tenantcontroller', 'update', substr($err->getMessage(), 0, 1000));
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

            if ($this->tenantService->delete($id)) {
                $message = 'Tenant deleted successfully';
                $this->storeAdminWorkLog($id, 'tenants', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To Delete Tenant.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'Tenantcontroller', 'destroy', substr($err->getMessage(), 0, 1000));
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
            $dataInfo = $this->tenantService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'Tenant ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'tenants', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " Tenant.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'TenantController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors(['error' => $message]);
        }
    }
}
