<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Throwable;
use App\Models\Jenis;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DocumentTemplate;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Crypt;

class DocumentTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->can('create template')) {
            if (request()->type == 'datatable') {
                $data = DocumentTemplate::with('jenis')
                    ->get()->sortBy('jenis.nama');

                return datatables()->of($data)
                    ->addColumn('action', function ($data) use ($user) {
                        $editRoute       = 'admin.document-template.edit';
                        $deleteRoute     = 'admin.document-template.destroy';
                        $dataId          = Crypt::encryptString($data->id);
                        $dataDeleteLabel = 'Document Template ' . $data->jenis->nama;

                        $action = "";

                        if ($user->can('update template')) {
                            $action .= '
                            <a class="btn btn-warning btn-icon" type="button" href="' . route($editRoute, $dataId) . '">
                                <i data-feather="edit"></i>
                            </a> ';
                        }

                        if ($user->can('delete template')) {
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
                    ->addColumn('jenis', function ($data) {
                        return $data->jenis->nama;
                    })
                    ->addColumn('template', function ($data) {
                        $showRoute     = 'admin.document-template.show';
                        $dataId          = Crypt::encryptString($data->id);

                        return $data->template ?
                            '<a class="btn btn-info" type="button" target="_blank" href="' . route($showRoute, $dataId) . '">
                            Show Template
                        </a> ' : '';
                    })
                    ->rawColumns(['action', 'jenis', 'template'])
                    ->make(true);
            }

            return view('admin.modules.document-template.index', [
                'breadcrumb' => 'Document Templates'
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
        $user = auth()->user();

        if ($user->can('create template')) {
            return view('admin.modules.document-template.create', [
                'breadcrumb'    => 'Document Template',
                'btnSubmit'     => 'Simpan',
                'jenis'         => Jenis::orderBy('nama', 'asc')->get(['id', 'nama']),
            ]);
        } else {
            return View::make('error.403');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->can('create template')) {
            $request->validate([
                'jenis_document'    => 'required|unique:document_templates,jenis_id',
                'isi_document'      => 'required|min:5'
            ]);

            try {
                DB::beginTransaction();

                DocumentTemplate::create([
                    'jenis_id'  => $request->jenis_document,
                    'template'  => $request->isi_document
                ]);

                DB::commit();

                if (isset($_POST['btnSimpan'])) {
                    return redirect()->route('admin.document-template.index')
                        ->with('success', 'Document template has been created');
                } else {
                    return redirect()->route('admin.document-template.create')
                        ->with('success', 'Document template has been created');
                }
            } catch (Exception $e) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
            } catch (Throwable $e) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
            }
        } else {
            DB::rollBack();
            return View::make('error.403');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $id = Crypt::decryptString($id);
        $data = DocumentTemplate::with([
            'jenis' => function ($query) {
                return $query->select('id', 'nama');
            },
        ])->find($id);

        $pdf = Pdf::loadView('admin.modules.document-template.show', [
            'data'      => $data
        ]);
        return $pdf->setPaper('A4', 'portrait')->stream();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();

        if ($user->can('update template')) {
            try {
                $decrypt = Crypt::decryptString($id);
                $data = DocumentTemplate::find($decrypt);
                $jenis = Jenis::orderBy('nama', 'asc')->get(['id', 'nama']);

                if (!$data) {
                    return redirect()
                        ->back()
                        ->with('error', "Data tidak ditemukan");
                }

                return view('admin.modules.document-template.edit', [
                    'breadcrumb'    => 'Document Template',
                    'btnSubmit'     => 'Simpan Perubahan',
                    'data'          => $data,
                    'jenis'         => $jenis
                ]);
            } catch (\Throwable $e) {
                return redirect()
                    ->back()
                    ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
            }
        } else {
            return View::make('error.403');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if ($user->can('update template')) {
            $id = Crypt::decryptString($id);
            $data = DocumentTemplate::find($id);

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data not found");
            }

            $request->validate([
                'jenis_document'    => 'required|unique:document_templates,jenis_id,' . $data->id,
                'isi_document'      => 'required|min:5'
            ]);

            DB::beginTransaction();
            try {
                $data->update([
                    'jenis_id'      => $request->jenis_document,
                    'template'      => $request->isi_document
                ]);

                DB::commit();
                return redirect()->route('admin.document-template.index')
                    ->with('success', 'Document template has been updated');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
            } catch (\Throwable $e) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
            }
        } else {
            DB::rollBack();
            return View::make('error.403');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if ($user->can('delete template')) {
            DB::beginTransaction();
            try {
                $id = Crypt::decryptString($id);
                $data = DocumentTemplate::find($id);

                if (!$data) {
                    return response()->json([
                        'status'  => 404,
                        'message' => "Data not found!",
                    ], 404);
                }

                $data->delete();


                DB::commit();

                return response()->json([
                    'status'  => 200,
                    'message' => "Document template has been deleted",
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
