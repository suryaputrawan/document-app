<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\CertificateType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class CertificateTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->type == 'datatable') {
            $data = CertificateType::orderBy('name', 'asc')->get(['id', 'name']);

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $user            = auth()->user();
                    $editRoute       = 'admin.certificate-types.edit';
                    $deleteRoute     = 'admin.certificate-types.destroy';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataDeleteLabel = $data->name;

                    $action = "";

                    if ($user->can('update certificate type')) {
                        $action .= '
                        <a class="btn btn-warning btn-icon" id="btn-edit" type="button" data-url="' . route($editRoute, $dataId) . '">
                            <i data-feather="edit"></i>
                        </a> ';
                    }

                    if ($user->can('delete certificate type')) {
                        $action .= '
                        <button class="btn btn-danger btn-icon delete-item" 
                            data-label="' . $dataDeleteLabel . '" data-url="' . route($deleteRoute, $dataId) . '">
                            <i data-feather="trash"></i>
                        </button> ';
                    }

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '
                    </div>';
                    return $group;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.modules.certificate-type.index', [
            'breadcrumb' => 'Certificate Types'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make([
            'name' => $request->name
        ], [
            'name' => 'required|min:3|unique:certificate_types,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            DB::beginTransaction();
            try {
                CertificateType::create([
                    'name' => Str::upper($request->name)
                ]);
                DB::commit();
                return response()->json([
                    'status'  => 200,
                    'message' => 'Certificate type has been success to created',
                ], 200);
            } catch (Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'status'  => 500,
                    'message' => $th->getMessage(),
                ], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CertificateType $certificateType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $id = Crypt::decryptString($id);
            $data = CertificateType::find($id);

            if ($data) {
                return response()->json([
                    'status' => 200,
                    'data' => $data,
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Certificate type not found',
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = CertificateType::find($id);

        $validator = Validator::make([
            'name' => $request->name
        ], [
            'name' => 'required|min:3|unique:certificate_types,name,' . $data->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            if ($data) {
                DB::beginTransaction();
                try {
                    $data->update([
                        'name' => Str::upper($request->name)
                    ]);
                    DB::commit();
                    return response()->json([
                        'status'  => 200,
                        'message' => 'Certificate type has been updated',
                    ], 200);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => 500,
                        'message' => $th->getMessage(),
                    ], 500);
                }
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Data not found..!',
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $id = Crypt::decryptString($id);
            $data = CertificateType::find($id);

            if (!$data) {
                return response()->json([
                    'status'  => 404,
                    'message' => "Data not found!",
                ], 404);
            }

            $data->delete();

            return response()->json([
                'status'  => 200,
                'message' => "Certificate type has been deleted",
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }
}
