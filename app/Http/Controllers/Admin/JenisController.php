<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use App\Models\Jenis;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class JenisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->can('create jenis')) {
            if (request()->type == 'datatable') {
                $data = Jenis::latest()->get(['id', 'nama']);

                return datatables()->of($data)
                    ->addColumn('action', function ($data) use ($user) {
                        $editRoute       = 'admin.jenis.edit';
                        $deleteRoute     = 'admin.jenis.destroy';
                        $dataId          = Crypt::encryptString($data->id);
                        $dataDeleteLabel = $data->nama;

                        $action = "";

                        if ($user->can('update jenis')) {
                            $action .= '
                            <a class="btn btn-warning btn-icon" id="btn-edit" type="button" data-url="' . route($editRoute, $dataId) . '">
                                <i data-feather="edit"></i>
                            </a> ';
                        }

                        if ($user->can('delete jenis')) {
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

            return view('admin.modules.jenis.index', [
                'breadcrumb' => 'Jenis Dokument'
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

        if ($user->can('create jenis')) {
            $validator = Validator::make([
                'nama' => $request->nama
            ], [
                'nama' => 'required|max:100|min:5|unique:jenis,nama',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {
                DB::beginTransaction();
                try {
                    Jenis::create([
                        'nama' => Str::upper($request->nama)
                    ]);
                    DB::commit();
                    return response()->json([
                        'status'  => 200,
                        'message' => 'Jenis document has been success to created',
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
    public function show(Jenis $jenis)
    {
        return View::make('error.403');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();

        if ($user->can('update jenis')) {
            try {
                $id = Crypt::decryptString($id);
                $data = Jenis::find($id);

                if ($data) {
                    return response()->json([
                        'status' => 200,
                        'data' => $data,
                    ]);
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Jenis Document Not Found',
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

        if ($user->can('update jenis')) {
            $data = Jenis::find($id);

            $validator = Validator::make([
                'nama' => $request->nama
            ], [
                'nama' => 'required|max:100|min:5|unique:jenis,nama,' . $data->id,
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
                            'nama' => Str::upper($request->nama)
                        ]);
                        DB::commit();
                        return response()->json([
                            'status'  => 200,
                            'message' => 'Jenis document has been updated',
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

        if ($user->can('delete jenis')) {
            try {
                $id = Crypt::decryptString($id);
                $data = Jenis::find($id);

                if (!$data) {
                    return response()->json([
                        'status'  => 404,
                        'message' => "Data not found!",
                    ], 404);
                }

                $data->delete();

                return response()->json([
                    'status'  => 200,
                    'message' => "Jenis document has been deleted",
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
