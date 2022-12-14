<?php

namespace App\Http\Controllers;

use App\Interfaces\PaymentMethodRepositoryInterface;
use App\Models\PaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class PaymentMethodController extends Controller
{
    private PaymentMethodRepositoryInterface $paymentMethodRepository;

    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function index()
    {
        return view('master/payment_method/payment_method');
    }

    public function aksi($data)
    {
        $edit = '';
        $delete = '';
        if (Auth::user()->akses('edit')) {

            $edit = '<a class="dropdown-item text-info" href="javascript:;" onclick="edit(\'' . $data->id . '\')">' .
                '<i class="fa fa-edit"></i>&nbsp;&nbsp;&nbsp;Edit' .
                '</a>';
        }

        if (Auth::user()->akses('delete')) {
            $delete = '<a class="dropdown-item text-danger" href="javascript:;" onclick="hapus(\'' . $data->id . '\')">' .
                '<i class="fa fa-trash"></i>&nbsp;&nbsp;&nbsp;Delete' .
                '</a>';
        }


        return '<div class="dropdown show">' .
            '  <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' .
            '<i class="fa fa-bars"></i>' .
            '  </a>' .

            '<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">' .
            $edit .
            $delete .
            '  </div>' .
            '</div>';

        return '<div class="dropdown">' .
            '<button class="dropdown-toggle btn px-2 box" aria-expanded="false" data-tw-toggle="dropdown">' .
            '<span class="w-5 h-5 flex items-center justify-center">' .
            '<i class="fa fa-bars"></i>' .
            '</span>' .
            '</button>' .
            '<div class="dropdown-menu w-52 ">' .
            '<ul class="dropdown-content">' .
            $edit .
            $delete .
            '</ul>' .
            '</div>' .
            '</div>';
    }

    public function datatable(Request $req): JsonResponse
    {
        $data = PaymentMethod::get();
        return DataTables::of($data)
            ->addColumn('aksi', function ($data) {
                return $this->aksi($data);
            })
            ->addColumn('status', function ($data) {
                if ($data->status == true) {
                    return '<button class="btn btn-success btn-round btn-xs" onclick="gantiStatus(0,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                } else {
                    return '<button class="btn btn-danger btn-round btn-xs" onclick="gantiStatus(1,\'' . $data->id . '\')"><i class="fa fa-check-circle"></i></button>';
                }
            })
            ->addColumn('image', function ($data) {
                return '<img class="rounded" width="75" height="75" src="' . $data->image . '"></img>';
            })
            ->rawColumns(['aksi', 'status', 'icon', 'image'])
            ->addIndexColumn()
            ->make(true);
    }


    public function store(Request $req): JsonResponse
    {

        return DB::transaction(function () use ($req) {
            $orderId = $req->id;
            if ($orderId == '' or $orderId == null) {

                $file = $req->file('image');
                if ($file != null) {
                    $path = 'image/payment_method';
                    $id = Str::uuid($req->id)->toString();
                    $name = $id . '.' . str_replace('image/', '', $file->getClientOriginalExtension());
                    $foto = $path . '/' . $name;
                    if (is_file($foto)) {
                        unlink($foto);
                    }

                    if (!file_exists($path)) {
                        $oldmask = umask(0);
                        mkdir($path, 0777, true);
                        umask($oldmask);
                    }

                    $img = Image::make(file_get_contents($file))->encode(str_replace('image/', '', $file->getMimeType()), 12);
                    $img->save($foto);
                }


                $paymentMethodDetails = [
                    'id' => $this->paymentMethodRepository->getIdPaymentMethod(),
                    'name' => convertSlug($req->name, 'Capital'),
                    'description' => $req->description,
                    'no_ref' => $req->no_ref,
                    'nama_ref' => $req->nama_ref,
                    'jenis' => $req->jenis,
                    'image' => url('/') . '/' . $foto,
                    'status' => 1,
                    'created_by' => me(),
                    'updated_by' => me(),
                ];

                return response()->json(
                    [
                        'status' => 1,
                        'message' => 'Berhasil menyimpan data',
                        'data' => $this->paymentMethodRepository->createPaymentMethod($paymentMethodDetails)
                    ],
                    Response::HTTP_CREATED
                );
            } else {

                $file = $req->file('image');
                if ($file != null) {
                    $path = 'image/payment_method';
                    $id = Str::uuid($req->id)->toString();
                    $name = $id . '.' . str_replace('image/', '', $file->getClientOriginalExtension());
                    $foto = $path . '/' . $name;
                    if (is_file($foto)) {
                        unlink($foto);
                    }

                    if (!file_exists($path)) {
                        $oldmask = umask(0);
                        mkdir($path, 0777, true);
                        umask($oldmask);
                    }

                    $img = Image::make(file_get_contents($file))->encode(str_replace('image/', '', $file->getMimeType()), 12);
                    $img->save($foto);
                    $foto = url('/') . '/' . $foto;
                } else {
                    $foto = $this->paymentMethodRepository->getPaymentMethodById($req->id)->image;
                }

                $roleDetails = [
                    'name' => convertSlug($req->name, 'Capital'),
                    'description' => $req->description,
                    'image' => $foto,
                    'no_ref' => $req->no_ref,
                    'nama_ref' => $req->nama_ref,
                    'jenis' => $req->jenis,
                    'updated_by' => me(),
                ];

                return response()->json(
                    [
                        'status' => 1,
                        'message' => 'Berhasil merubah data',
                        'data' => $this->paymentMethodRepository->updatePaymentMethod($orderId, $roleDetails)
                    ],
                    Response::HTTP_CREATED
                );
            }
        });
    }

    public function edit(Request $request): JsonResponse
    {
        $orderId = $request->id;

        return response()->json([
            'data' => $this->paymentMethodRepository->getPaymentMethodById($orderId),
            'message' => 'Berhasil merubah status data',
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $orderId = $request->id;

        $this->paymentMethodRepository->deletePaymentMethod($orderId);

        return response()->json(['status' => 1], 200);
    }

    public function gantiStatus(Request $req): JsonResponse
    {
        $orderId = $req->id;
        $roleDetails = [
            'status' => $req->param,
            'updated_by' => me(),
        ];


        return response()->json([
            'data' => $this->paymentMethodRepository->updatePaymentMethod($orderId, $roleDetails),
            'message' => 'Berhasil merubah status data',
        ]);
    }
}
