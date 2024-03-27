<?php

namespace App\Http\Controllers\Admin;

use Throwable;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->can('create karyawan')) {
            if (request()->type == 'datatable') {
                $data = Karyawan::orderBy('nama', 'ASC')->get();

                return datatables()->of($data)
                    ->addColumn('action', function ($data) use ($user) {
                        $editRoute       = 'admin.karyawan.edit';
                        $deleteRoute     = 'admin.karyawan.destroy';
                        $dataId          = Crypt::encryptString($data->id);
                        $dataDeleteLabel = $data->nama;

                        $action = "";

                        if ($user->can('update karyawan')) {
                            $action .= '
                            <a class="btn btn-warning btn-icon" id="btn-edit" type="button" data-url="' . route($editRoute, $dataId) . '">
                                <i data-feather="edit"></i>
                            </a> ';
                        }

                        if ($user->can('delete karyawan')) {
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
                    ->addColumn('gambar', function ($data) {
                        return $data->ttd_picture ? '<img src="' . $data->takeImage . '" alt="Gambar" width="50">' : '';
                    })
                    ->rawColumns(['action', 'gambar'])
                    ->make(true);
            }

            return view('admin.modules.karyawan.index', [
                'breadcrumb' => 'Karyawan'
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

        if ($user->can('create karyawan')) {
            $validator = Validator::make([
                'nama'      => $request->nama,
                'nip'       => $request->nip,
                'jabatan'   => $request->jabatan,
                'email'     => $request->email,
                'gambar'    => $request->gambar
            ], [
                'nama'      => 'required|max:100|min:3|unique:karyawan,nama,NULL,id',
                'email'     => 'required|unique:users,email',
                'gambar'    =>  request('gambar') ? 'mimes:jpg,jpeg,png|max:1000' : '',
                'jabatan'   => 'required|min:5',
                'nip'       => 'required|min:10|unique:karyawan,nip'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {
                DB::beginTransaction();
                try {
                    $karyawan = Karyawan::create([
                        'nama'          => $request->nama,
                        'nip'           => $request->nip,
                        'jabatan'       => $request->jabatan,
                        'ttd_picture'   => request('gambar') ? $request->file('gambar')->store('ttd') : null
                    ]);
                    User::create([
                        'name'          => $request->nama,
                        'username'      => $request->nip,
                        'email'         => $request->email,
                        'password'      => bcrypt($request->nip),
                        'karyawan_id'   => $karyawan->id,
                    ]);
                    DB::commit();
                    return response()->json([
                        'status'  => 200,
                        'message' => 'Karyawan berhasil ditambahkan',
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
    public function show(Karyawan $karyawan)
    {
        return View::make('error.403');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();

        if ($user->can('update karyawan')) {
            try {
                $id = Crypt::decryptString($id);
                $data = Karyawan::with('user')->find($id);

                if ($data) {
                    return response()->json([
                        'status' => 200,
                        'data' => $data,
                    ]);
                } else {
                    return response()->json([
                        'status' => 404,
                        'message' => 'Karyawan Not Found',
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

        if ($user->can('update karyawan')) {
            $data = Karyawan::with([
                'user'  => function ($query) {
                    return $query->select('id', 'email', 'karyawan_id');
                }
            ])->find($id);

            $user = User::where('karyawan_id', $data->id)
                ->first(['id', 'name', 'email', 'karyawan_id']);

            $validator = Validator::make([
                'nama'      => $request->nama,
                'nip'       => $request->nip,
                'jabatan'   => $request->jabatan,
                'email'     => $request->email,
                'gambar'    => $request->gambar
            ], [
                'nama'      => 'required|max:100|min:3|unique:karyawan,nama,' . $data->id,
                'email'     => 'required|unique:users,email,' . $data->user->id,
                'gambar'    => request('gambar') ? 'mimes:jpg,jpeg,png|max:1000' : '',
                'jabatan'   => 'required|min:5',
                'nip'       => 'required|min:10|unique:karyawan,nip,' . $data->id
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {
                if ($data) {
                    DB::beginTransaction();

                    //Membuat kondisi langsung mendelete gambar yang lama pada storage
                    if (request('gambar')) {
                        if ($data->ttd_picture) {
                            Storage::delete($data->ttd_picture);
                        }
                        $picture = request()->file('gambar')->store('ttd');
                    } elseif ($data->ttd_picture) {
                        $picture = $data->ttd_picture;
                    } else {
                        $picture = null;
                    }

                    try {
                        $data->update([
                            'nama'          => $request->nama,
                            'nip'           => $request->nip,
                            'jabatan'       => $request->jabatan,
                            'ttd_picture'   => $picture,
                        ]);

                        $user->update([
                            'name'      => $request->nama,
                            'username'  => $request->nip,
                            'email'     => $request->email
                        ]);
                        DB::commit();

                        return response()->json([
                            'status'  => 200,
                            'message' => 'Karyawan has been updated',
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

        if ($user->can('delete karyawan')) {
            DB::beginTransaction();
            try {
                $id = Crypt::decryptString($id);
                $data = Karyawan::find($id);

                if (!$data) {
                    return response()->json([
                        'status'  => 404,
                        'message' => "Data not found!",
                    ], 404);
                }

                //Kondisi apabila terdapat path gambar pada tabel slider
                if ($data->ttd_picture != null) {
                    Storage::delete($data->ttd_picture);
                }

                $data->delete();

                DB::commit();

                return response()->json([
                    'status'  => 200,
                    'message' => "Karyawan telah berhasil dihapus",
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
