<?php

namespace App\Http\Controllers\Client;

use Throwable;
use App\Models\Certificate;
use Illuminate\Http\Request;
use App\Models\CertificateType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $certificateEndDate = Certificate::get();

        foreach ($certificateEndDate as $dataCertificate) {
            $dataEndDate = Certificate::where('end_date', '<=', Carbon::now()->addDay(7))->get();
        }

        if (request()->type == 'datatable') {
            $data = Certificate::latest()->get();

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $user            = auth()->user();
                    $editRoute       = 'certificates.edit';
                    $viewRoute       = 'certificates.show';
                    $deleteRoute     = 'certificates.destroy';
                    $dataId          = Crypt::encryptString($data->id);
                    $dataDeleteLabel = $data->name;

                    $action = "";

                    if ($user->can('update certificate')) {
                        $action .= '
                        <a class="btn btn-info btn-icon" id="btn-view" type="button" data-url="' . route($viewRoute, $dataId) . '">
                            <i data-feather="eye"></i>
                        </a> ';
                    }

                    if ($user->can('update certificate')) {
                        $action .= '
                        <a class="btn btn-warning btn-icon" id="btn-edit" type="button" data-url="' . route($editRoute, $dataId) . '">
                            <i data-feather="edit"></i>
                        </a> ';
                    }

                    if ($user->can('delete certificate')) {
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
                ->editColumn('start_date', function ($data) {
                    return '<span class="text-primary">' . Carbon::parse($data->start_date)->format('d M Y') . '</span>';
                })
                ->editColumn('end_date', function ($data) {
                    return '<span class="text-danger">' . Carbon::parse($data->end_date)->format('d M Y') . '</span>';
                })
                ->addColumn('type', function ($data) {
                    return $data->certificateType->name;
                })
                ->rawColumns(['action', 'type', 'start_date', 'end_date'])
                ->make(true);
        }

        return view('certificate.index', [
            'breadcrumb'    => 'Certificates',
            'types'         => CertificateType::orderBy('name', 'asc')->get(['id', 'name']),
            'dataEndDate'       => $dataEndDate
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
        $user = auth()->user();

        if ($user->can('create certificate')) {
            $validator = Validator::make([
                'certificate_number'    => $request->certificate_number,
                'type'                  => $request->type,
                'name'                  => $request->name,
                'start_date'            => $request->start_date,
                'end_date'              => $request->end_date,
                'employee'              => $request->employee,
                'file'                  => $request->file
            ], [
                'certificate_number'    => 'required|min:5|unique:certificates,certificate_number',
                'type'                  => 'required',
                'name'                  => 'required|min:5',
                'start_date'            => 'required',
                'end_date'              => 'required',
                'employee'              => 'required|min:5',
                'file'                  => 'required|mimes:jpg,jpeg,png,pdf|max:1000',
            ]);

            if ($request->start_date > $request->end_date) {
                return response()->json([
                    'status' => 400,
                    'errors' => [
                        'start_date'    => ['start date cannot be greater than end date'],
                        'end_date'      => ['end date cannot be smaller than start date']
                    ],
                ]);
            }

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {
                DB::beginTransaction();
                try {
                    Certificate::create([
                        'certificate_number'    => $request->certificate_number,
                        'certificate_type_id'   => $request->type,
                        'name'                  => $request->name,
                        'start_date'            => $request->start_date,
                        'end_date'              => $request->end_date,
                        'employee_name'         => $request->employee,
                        'file'                  => $request->file('file')->store('certificates'),
                        'user_created'          => auth()->user()->id,
                    ]);
                    DB::commit();
                    return response()->json([
                        'status'  => 200,
                        'message' => 'Certificate has been created',
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
            abort(403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();

        if ($user->can('update certificate')) {
            try {
                $id = Crypt::decryptString($id);
                $data = Certificate::with('certificateType')->find($id);

                if ($data) {
                    return response()->json([
                        'status'    => 200,
                        'data'      => $data,
                    ]);
                } else {
                    return response()->json([
                        'status'    => 404,
                        'message'   => 'Data Not Found',
                    ]);
                }
            } catch (\Throwable $e) {
                return response()->json([
                    'status'  => 500,
                    'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
                ], 500);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();

        if ($user->can('update certificate')) {
            try {
                $id = Crypt::decryptString($id);
                $data = Certificate::find($id);

                if ($data) {
                    return response()->json([
                        'status'    => 200,
                        'data'      => $data,
                    ]);
                } else {
                    return response()->json([
                        'status'    => 404,
                        'message'   => 'Data Not Found',
                    ]);
                }
            } catch (\Throwable $e) {
                return response()->json([
                    'status'  => 500,
                    'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
                ], 500);
            }
        } else {
            abort(403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if ($user->can('update certificate')) {
            $data = Certificate::find($id);

            $validator = Validator::make([
                'certificate_number'    => $request->certificate_number,
                'type'                  => $request->type,
                'name'                  => $request->name,
                'start_date'            => $request->start_date,
                'end_date'              => $request->end_date,
                'employee'              => $request->employee,
                'file'                  => $request->file
            ], [
                'certificate_number'    => 'required|min:5|unique:certificates,certificate_number,' . $data->id,
                'type'                  => 'required',
                'name'                  => 'required|min:5',
                'start_date'            => 'required',
                'end_date'              => 'required',
                'employee'              => 'required|min:5',
                'file'                  => request('file') ? 'mimes:jpg,jpeg,png,pdf|max:1000' : '',
            ]);

            if ($request->start_date > $request->end_date) {
                return response()->json([
                    'status' => 400,
                    'errors' => [
                        'start_date'    => ['start date cannot be greater than end date'],
                        'end_date'      => ['end date cannot be smaller than start date']
                    ],
                ]);
            }

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'errors' => $validator->messages(),
                ]);
            } else {
                if ($data) {
                    DB::beginTransaction();

                    if (request('file')) {
                        if ($data->file) {
                            Storage::delete($data->file);
                        }
                        $picture = request()->file('file')->store('certificates');
                    } elseif ($data->file) {
                        $picture = $data->file;
                    } else {
                        $picture = null;
                    }

                    try {
                        $data->update([
                            'certificate_number'    => $request->certificate_number,
                            'type'                  => $request->type,
                            'name'                  => $request->name,
                            'start_date'            => $request->start_date,
                            'end_date'              => $request->end_date,
                            'employee_name'         => $request->employee,
                            'file'                  => $picture,
                        ]);

                        DB::commit();

                        return response()->json([
                            'status'  => 200,
                            'message' => 'Certificate has been updated',
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
            abort(403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if ($user->can('delete certificate')) {
            DB::beginTransaction();
            try {
                $id = Crypt::decryptString($id);
                $data = Certificate::find($id);

                if (!$data) {
                    return response()->json([
                        'status'  => 404,
                        'message' => "Data not found!",
                    ], 404);
                }

                if ($data->file != null) {
                    Storage::delete($data->file);
                    $data->delete();
                } else {
                    $data->delete();
                }

                DB::commit();

                return response()->json([
                    'status'  => 200,
                    'message' => "Certificate has been deleted..!",
                ], 200);
            } catch (\Throwable $e) {
                return response()->json([
                    'status'  => 500,
                    'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
                ], 500);
            }
        } else {
            abort(403);
        }
    }
}
