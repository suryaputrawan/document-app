<?php

namespace App\Http\Controllers\Permissions;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index()
    {
        if (request()->type == 'datatable') {
            $data = Permission::get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $editRoute       = 'admin.permissions.edit';
                    $dataId          = Crypt::encryptString($data->id);

                    $action = "";
                    $action .= '
                        <a class="btn btn-warning btn-icon" id="btn-edit" type="button" data-url="' . route($editRoute, $dataId) . '">
                            <i data-feather="edit"></i>
                        </a> ';

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '
                    </div>';
                    return $group;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.permission.permissions.index', [
            'breadcrumb' => 'Permissions'
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make([
            'name'          => $request->name,
            'guard_name'    => $request->guard_name,
        ], [
            'name' => 'required|max:100|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            DB::beginTransaction();
            try {
                Permission::create([
                    'name'          => request('name'),
                    'guard_name'    => request('guard_name') ?? 'web',
                ]);
                DB::commit();
                return response()->json([
                    'status'  => 200,
                    'message' => 'Permission has been success to created',
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

    public function edit($id)
    {
        try {
            $id = Crypt::decryptString($id);
            $data = Permission::find($id);

            if ($data) {
                return response()->json([
                    'status' => 200,
                    'data' => $data,
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Permission Not Found',
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $data = Permission::find($id);

        $validator = Validator::make([
            'name'          => $request->name,
            'guard_name'    => $request->guard_name
        ], [
            'name' => 'required|max:100|min:3',
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
                        'name'          => $request->name,
                        'guard_name'    => $request->guard_name
                    ]);
                    DB::commit();
                    return response()->json([
                        'status'  => 200,
                        'message' => 'Permission has been updated',
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
}
