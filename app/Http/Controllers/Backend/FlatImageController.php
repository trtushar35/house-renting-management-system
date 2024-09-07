<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\FlatImageRequest;
use App\Services\FlatService;
use Illuminate\Support\Facades\DB;
use App\Services\FlatImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use App\Traits\SystemTrait;
use Exception;

class FlatImageController extends Controller
{
    use SystemTrait;

    protected $flatimageService, $flatService;

    public function __construct(FlatImageService $flatimageService, FlatService $flatService)
    {
        $this->flatimageService = $flatimageService;
        $this->flatService = $flatService;
    }

    public function index()
    {
        return Inertia::render(
            'Backend/FlatImage/Index',
            [
                'pageTitle' => fn() => 'FlatImage List',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'FlatImage Manage'],
                    ['link' => route('backend.flatimage.index'), 'title' => 'FlatImage List'],
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
            ['fieldName' => 'flat_id', 'class' => 'text-center'],
            ['fieldName' => 'square_footage', 'class' => 'text-center'],
            ['fieldName' => 'status', 'class' => 'text-center'],
        ];
    }
    private function getTableHeaders()
    {
        return [
            'Sl/No',
            'Flat Number',
            'Square Footage',
            'Status',
            'Action'
        ];
    }

    private function getDatas()
    {
        $query = $this->flatimageService->list();

        if (request()->filled('flat_number'))
            $query->whereHas('flat', function ($query) {
                $query->where('flat_number', 'like', '%' . request()->flat_number . '%');
            });

        $datas = $query->paginate(request()->numOfData ?? 10)->withQueryString();

        $formatedDatas = $datas->map(function ($data, $index) {
            $customData = new \stdClass();
            $customData->index = $index + 1;

            $customData->flat_id = $data->flat->flat_number;
            $customData->square_footage = $data->square_footage;

            $customData->status = getStatusText($data->status);
            $customData->hasLink = true;
            $customData->links = [

                [
                    'linkClass' => 'semi-bold text-white statusChange ' . (($data->status == 'Active') ? "bg-gray-500" : "bg-green-500"),
                    'link' => route('backend.flatimage.status.change', ['id' => $data->id, 'status' => $data->status == 'Active' ? 'Inactive' : 'Active']),
                    'linkLabel' => getLinkLabel((($data->status == 'Active') ? "Inactive" : "Active"), null, null)
                ],

                [
                    'linkClass' => 'bg-yellow-400 text-black semi-bold',
                    'link' => route('backend.flatimage.edit', $data->id),
                    'linkLabel' => getLinkLabel('Edit', null, null)
                ],
                [
                    'linkClass' => 'deleteButton bg-red-500 text-white semi-bold',
                    'link' => route('backend.flatimage.destroy', $data->id),
                    'linkLabel' => getLinkLabel('Delete', null, null)
                ]
            ];
            return $customData;
        });

        return regeneratePagination($formatedDatas, $datas->total(), $datas->perPage(), $datas->currentPage());
    }

    public function create()
    {
        $flatDetails = $this->flatService->activeList();
        return Inertia::render(
            'Backend/FlatImage/Form',
            [
                'pageTitle' => fn() => 'FlatImage Create',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'FlatImage Manage'],
                    ['link' => route('backend.flatimage.create'), 'title' => 'FlatImage Create'],
                ],
                "flatDetails" => $flatDetails,
            ]
        );
    }


    public function store(FlatImageRequest $request)
    {

        DB::beginTransaction();
        try {

            $data = $request->validated();

            if ($request->hasFile('square_footage')) {
                $images = [];
                foreach ($request->file('square_footage') as $image) {
                    $images[] = $this->imageUpload($image, 'flatImage');
                }
                $data["square_footage"] = implode(',', $images);
            }

            $dataInfo = $this->flatimageService->create($data);

            if ($dataInfo) {
                $message = 'FlatImage created successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'flat_images', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To create FlatImage.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {

            DB::rollBack();
            $this->storeSystemError('Backend', 'FlatImageController', 'store', substr($err->getMessage(), 0, 1000));

            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";

            return redirect()
                ->back()
                ->with('errorMessage', $message);
        }
    }

    public function edit($id)
    {
        $flatimage = $this->flatimageService->find($id);
        $flatDetails = $this->flatService->activeList();

        return Inertia::render(
            'Backend/FlatImage/Form',
            [
                'pageTitle' => fn() => 'FlatImage Edit',
                'breadcrumbs' => fn() => [
                    ['link' => null, 'title' => 'FlatImage Manage'],
                    ['link' => route('backend.flatimage.edit', $id), 'title' => 'FlatImage Edit'],
                ],
                'flatimage' => fn() => $flatimage,
                'id' => fn() => $id,
                "flatDetails" => $flatDetails,
            ]
        );
    }

    public function update(FlatImageRequest $request, $id)
    {
        DB::beginTransaction();
        try {

            $data = $request->validated();
            $FlatImage = $this->flatimageService->find($id);
            if ($request->hasFile('square_footage')) {
                $images = [];
                foreach ($request->file('square_footage') as $image) {
                    $images[] = $this->imageUpload($image, 'flatImage');
                }
                $data["square_footage"] = implode(',', $images);
            }

            $dataInfo = $this->flatimageService->update($data, $id);

            if ($dataInfo->save()) {
                $message = 'FlatImage updated successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'flat_images', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To update FlatImage.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'FlatImagecontroller', 'update', substr($err->getMessage(), 0, 1000));
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

            if ($this->flatimageService->delete($id)) {
                $message = 'FlatImage deleted successfully';
                $this->storeAdminWorkLog($id, 'flat_images', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To Delete FlatImage.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'FlatImagecontroller', 'destroy', substr($err->getMessage(), 0, 1000));
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
            $dataInfo = $this->flatimageService->changeStatus(request());

            if ($dataInfo->wasChanged()) {
                $message = 'FlatImage ' . request()->status . ' Successfully';
                $this->storeAdminWorkLog($dataInfo->id, 'flat_images', $message);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('successMessage', $message);
            } else {
                DB::rollBack();

                $message = "Failed To " . request()->status . " FlatImage.";
                return redirect()
                    ->back()
                    ->with('errorMessage', $message);
            }
        } catch (Exception $err) {
            DB::rollBack();
            $this->storeSystemError('Backend', 'FlatImageController', 'changeStatus', substr($err->getMessage(), 0, 1000));
            DB::commit();
            $message = "Server Errors Occur. Please Try Again.";
            return redirect()
                ->back()
                ->withErrors(['error' => $message]);
        }
    }
}
