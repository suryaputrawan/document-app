<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use App\Models\Hospital;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class HospitalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->can('create hospital')) {
            if (request()->type == 'datatable') {
                $data = Hospital::orderBy('name', 'asc')->get(['id', 'name']);

                return datatables()->of($data)
                    ->addColumn('action', function ($data) use ($user) {
                        $editRoute       = 'admin.hospitals.edit';
                        $deleteRoute     = 'admin.hospitals.destroy';
                        $dataId          = Crypt::encryptString($data->id);
                        $dataDeleteLabel = $data->name;

                        $action = "";

                        if ($user->can('update hospital')) {
                            $action .= '
                            <a class="btn btn-warning btn-icon" id="btn-edit" type="button" data-url="' . route($editRoute, $dataId) . '">
                                <i data-feather="edit"></i>
                            </a> ';
                        }

                        if ($user->can('delete hospital')) {
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

            return view('admin.modules.hospital.index', [
                'breadcrumb' => 'Hospitals'
            ]);
        } else {
            return View::make('error.403');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return View::make('error.403');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->can('create hospital')) {
            $validator = Validator::make([
                'name' => $request->name
            ], [
                'name' => 'required|min:3|unique:hospitals,name',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {
                DB::beginTransaction();
                try {
                    Hospital::create([
                        'name' => Str::upper($request->name)
                    ]);
                    DB::commit();
                    return response()->json([
                        'status'  => 200,
                        'message' => 'Hospital / Clinic has been success to created',
                    ], 200);
                } catch (Throwable $th) {
                    DB::rollBack();
                    return response()->json([
                        'status'  => 500,
                        'message' => $th->getMessage(),
                    ], 500);
                }
            }
        } else {
            return response()->json([
                'status'  => 500,
                'message' => 'User dont have permission',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Hospital $hospital)
    {
        return View::make('error.403');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();

        if ($user->can('update hospital')) {
            try {
                $id = Crypt::decryptString($id);
                $data = Hospital::find($id);

                if ($data) {
                    return response()->json([
                        'status' => 200,
                        'data' => $data,
                    ]);
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Data not found',
                    ]);
                }
            } catch (\Throwable $e) {
                return response()->json([
                    'status'  => 500,
                    'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
                ], 500);
            }
        } else {
            return response()->json([
                'status'  => 500,
                'message' => 'User dont have permission',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if ($user->can('update hospital')) {
            $data = Hospital::find($id);

            $validator = Validator::make([
                'name' => $request->name
            ], [
                'name' => 'required|min:3|unique:hospitals,name,' . $data->id,
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
                            'message' => 'Hospital / clinic has been updated',
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
        } else {
            return response()->json([
                'status'  => 500,
                'message' => 'User dont have permission',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if ($user->can('update hospital')) {
            try {
                $id = Crypt::decryptString($id);
                $data = Hospital::find($id);

                if (!$data) {
                    return response()->json([
                        'status'  => 404,
                        'message' => "Data not found!",
                    ], 404);
                }

                $data->delete();

                return response()->json([
                    'status'  => 200,
                    'message' => "Hospital / clinic has been deleted",
                ], 200);
            } catch (\Throwable $e) {
                return response()->json([
                    'status'  => 500,
                    'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
                ], 500);
            }
        } else {
            return response()->json([
                'status'  => 500,
                'message' => 'User dont have permission',
            ], 500);
        }
    }
}
