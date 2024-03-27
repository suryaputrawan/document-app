<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Jenis;
use App\Models\Document;
use App\Models\Karyawan;
use App\Mail\DocumentMail;
use Illuminate\Http\Request;
use App\Mail\DocumentSignMail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DocumentApproval;
use App\Models\DocumentTemplate;
use App\Models\DocumentRecipient;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\DocumentRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->type == 'datatable') {
            if (auth()->user()->username == "superadmin") {
                $data = Document::with('jenis')->latest()->get();
            } else {
                $user = auth()->user()->karyawan_id;
                $data = Document::with('recipient', 'approval', 'jenis')
                    ->whereHas('approval', function ($query) {
                        $query->where('id', auth()->user()->karyawan_id);
                    })
                    ->orWhereHas('recipient', function ($query) {
                        $query->where('id', auth()->user()->karyawan_id);
                    })
                    ->orWhere('pengirim_diajukan_oleh', $user)
                    ->orWhere('pengirim_disetujui_oleh', $user)
                    ->orwhere('created_by', $user)
                    ->latest()
                    ->get();
            }

            return datatables()->of($data)
                ->addColumn('action', function ($data) {
                    $user               = auth()->user();
                    $editRoute          = 'document.edit';
                    $deleteRoute        = 'document.destroy';
                    $viewRoute          = 'document.show';
                    $dataId             = Crypt::encryptString($data->id);
                    $dataDeleteLabel    = $data->no_surat;

                    $action = "";

                    $action .= '
                    <a class="btn btn-primary btn-icon" type="button" target="_blank" href="' . route($viewRoute, $dataId) . '">
                        <i data-feather="eye"></i>
                    </a> ';

                    if ($data->created_by == auth()->user()->karyawan_id || auth()->user()->username == 'superadmin') {
                        if ($data->status_pengirim_diajukan == 0 && $data->status_pengirim_disetujui == 0) {
                            if ($user->can('update document')) {
                                $action .= '
                                <a class="btn btn-warning btn-icon" type="button" href="' . route($editRoute, $dataId) . '">
                                    <i data-feather="edit"></i>
                                </a> ';
                            }

                            if ($user->can('delete document')) {
                                $action .= '
                                <button class="btn btn-danger btn-icon delete-item" 
                                    data-label="' . $dataDeleteLabel . '" data-url="' . route($deleteRoute, $dataId) . '">
                                    <i data-feather="trash"></i>
                                </button> ';
                            }
                        }
                    }

                    $group = '<div class="btn-group btn-group-sm mb-1 mb-md-0" role="group">
                        ' . $action . '</div>';

                    return $group;
                })
                ->addColumn('jenis_surat', function ($data) {
                    return $data->jenis->nama;
                })
                ->editColumn('tgl_surat', function ($data) {
                    return Carbon::parse($data->tgl_surat)->format('d M Y');
                })
                ->addColumn('sign', function ($data) {
                    $signRoute      = 'document.uploadSign';
                    $signatureRoute = 'document.signature';
                    $dataLabel      = $data->no_surat;
                    $dataId         = Crypt::encryptString($data->id);
                    $approvals      = $data->approval()->get();
                    $recipients     = $data->recipient()->get();

                    $sign = "";

                    foreach ($approvals as $approval) {
                        if (
                            $data->pengirim_diajukan_oleh == auth()->user()->karyawan_id && $data->status_pengirim_diajukan == 0 ||
                            $data->pengirim_disetujui_oleh == auth()->user()->karyawan_id && $data->status_pengirim_disetujui == 0 ||
                            $approval->pivot->karyawan_id == auth()->user()->karyawan_id && $approval->pivot->status_approval == 0
                        ) {
                            $sign .= '
                            <button class="btn btn-icon btn-info sign-document" 
                            data-label="' . $dataLabel . '" data-url="' . route($signRoute, $dataId) . '">
                            <i data-feather="upload"></i>
                            </button> ';

                            $sign .= '
                            <button class="btn btn-icon btn-success signature-doc" 
                            data-label="' . $dataLabel . '" data-url="' . route($signatureRoute, $dataId) . '">
                            <i data-feather="edit-3"></i>
                            </button> ';

                            return $sign;
                        }
                    }

                    foreach ($recipients as $recipient) {
                        if (
                            $recipient->pivot->karyawan_id == auth()->user()->karyawan_id && $recipient->pivot->status_recipient == 0
                        ) {
                            $sign .= '
                            <button class="btn btn-success sign-document" 
                            data-label="' . $dataLabel . '" data-url="' . route($signRoute, $dataId) . '">
                                Sign Doc
                            </button> ';

                            return $sign;
                        }
                    }
                })
                ->rawColumns(['action', 'jenis_surat', 'sign'])
                ->make(true);
        }

        return view('surat.index', [
            'breadcrumb' => 'Document',
            'jenis'      => Jenis::orderBy('nama', 'asc')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $user = auth()->user();

        if ($user->can('create document')) {
            try {
                $msJenis = Jenis::where('nama', $request->jenis_document)->first();
                $template = DocumentTemplate::where('jenis_id', $msJenis->id)->first();

                if (!$template) {
                    return redirect()
                        ->back()
                        ->with('error', "Template document not found..");
                }

                return view('surat.create', [
                    'breadcrumb'    => 'Document',
                    'btnSubmit'     => 'Simpan',
                    'jenis'         => Jenis::orderBy('nama', 'asc')->get(['id', 'nama']),
                    'karyawan'      => Karyawan::orderBy('nama', 'asc')->get(['id', 'nama', 'jabatan']),
                    'template'      => $template
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
     * Store a newly created resource in storage.
     */
    public function store(DocumentRequest $request)
    {
        $user = auth()->user();

        if ($user->can('create document')) {
            try {
                DB::beginTransaction();

                $document = Document::create([
                    'no_surat'                  => $request->no_surat,
                    'tgl_surat'                 => Carbon::now()->format('Y-m-d'),
                    'jenis_id'                  => $request->jenis_document,
                    'pengirim_diajukan_oleh'    => $request->diajukan_oleh,
                    'pengirim_disetujui_oleh'   => $request->disetujui_oleh,
                    'body'                      => $request->isi_document,
                    'created_by'                => auth()->user()->karyawan_id,
                ]);

                DB::commit();

                //Sync to table document_approval (many to many)
                $document->approval()->sync(request('approval'));

                //Sync to table document_recipient (many to many)
                $document->recipient()->sync(request('recipient'));

                // Send Email
                // To Pengirim Diajukan
                $diajukanOleh = User::where('karyawan_id', $document->pengirim_diajukan_oleh)
                    ->first(['id', 'name', 'email']);
                Mail::to($diajukanOleh->email)->send(new DocumentMail($document));

                // To Pengirim Disetujui
                $disetujuiOleh = User::where('karyawan_id', $document->pengirim_disetujui_oleh)
                    ->first(['id', 'name', 'email']);
                Mail::to($disetujuiOleh->email)->send(new DocumentMail($document));

                // To Recipient
                $docRecip = DocumentRecipient::where('document_id', $document->id)->get();
                foreach ($docRecip as $recipient) {
                    $userRecipient = User::where('karyawan_id', $recipient->karyawan_id)
                        ->first(['id', 'name', 'email']);
                    Mail::to($userRecipient->email)->send(new DocumentMail($document));
                }

                // To Approval
                $docApproval = DocumentApproval::where('document_id', $document->id)->get();
                foreach ($docApproval as $approval) {
                    $userApproval = User::where('karyawan_id', $approval->karyawan_id)
                        ->first(['id', 'name', 'email']);
                    Mail::to($userApproval->email)->send(new DocumentMail($document));
                }
                // End Send Email

                if (isset($_POST['btnSimpan'])) {
                    return redirect()->route('document.index')
                        ->with('success', 'Document has been created');
                } else {
                    return redirect()->route('document.create')
                        ->with('success', 'Document has been created');
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
            return View::make('error.403');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $id = Crypt::decryptString($id);
        $data = Document::with([
            'jenis' => function ($query) {
                return $query->select('id', 'nama');
            },
            'diajukanOleh'  => function ($query) {
                return $query->select('id', 'nama', 'jabatan', 'nip', 'ttd_picture');
            }
        ])->find($id);

        $pdf = Pdf::loadView('surat.show', [
            'data'      => $data,
            'approval'  => $data->approval,
            'recipient' => $data->recipient,
        ]);
        return $pdf->setPaper('A4', 'portrait')->stream();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();

        if ($user->can('update document')) {
            try {
                $id = Crypt::decryptString($id);
                $data = Document::find($id);
                $recipient = DocumentRecipient::where('document_id', $data->id)->get();
                $approval = DocumentApproval::where('document_id', $data->id)->get();

                if (!$data) {
                    return redirect()
                        ->back()
                        ->with('error', "Data not found..");
                }

                return view('surat.edit', [
                    'breadcrumb'    => 'Document',
                    'btnSubmit'     => 'Simpan Perubahan',
                    'data'          => $data,
                    'jenis'         => Jenis::orderBy('nama', 'asc')->get(['id', 'nama']),
                    'karyawan'      => Karyawan::orderBy('nama', 'asc')->get(['id', 'nama', 'jabatan']),
                    'recipient'     => $recipient,
                    'approval'      => $approval
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

        if ($user->can('update document')) {
            $id = Crypt::decryptString($id);
            $data = Document::find($id);
            $recipient = DocumentRecipient::where('document_id', $data->id)->get();
            $approval = DocumentApproval::where('document_id', $data->id)->get();

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data not found");
            }

            $request->validate([
                'no_surat'          => 'required|max:255|min:5|unique:documents,no_surat,' . $data->id,
                'jenis_document'    => 'required',
                'diajukan_oleh'     => 'required',
                'disetujui_oleh'    => 'required',
                'recipient'         => 'array',
                'approval'          => 'array',
                'isi_document'      => 'required|min:5',
            ]);

            DB::beginTransaction();
            try {
                $data->update([
                    'no_surat'                  => $request->no_surat,
                    'jenis_id'                  => $request->jenis_document,
                    'pengirim_diajukan_oleh'    => $request->diajukan_oleh,
                    'pengirim_disetujui_oleh'   => $request->disetujui_oleh,
                    'body'                      => $request->isi_document
                ]);

                $data->approval()->sync([]);
                $data->recipient()->sync([]);

                $data->approval()->sync(request('approval'));
                $data->recipient()->sync(request('recipient'));

                DB::commit();
                return redirect()->route('document.index')
                    ->with('success', 'Document has been updated');
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
            return View::make('error.403');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if ($user->can('delete document')) {
            DB::beginTransaction();
            try {
                $id = Crypt::decryptString($id);
                $data = Document::find($id);

                if (!$data) {
                    return response()->json([
                        'status'  => 404,
                        'message' => "Data not found!",
                    ], 404);
                }

                // Delete data document
                $data->delete();

                // Remove All data in many to many relation
                $data->approval()->sync([]);
                $data->recipient()->sync([]);

                DB::commit();

                return response()->json([
                    'status'  => 200,
                    'message' => "Document has been deleted",
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

    public function uploadSign(Request $request, $id)
    {
        $data = Karyawan::find(auth()->user()->karyawan_id);

        $validator = Validator::make([
            'picture'    => $request->picture
        ], [
            'picture'    => 'required|mimes:jpg,jpeg,png|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            if ($data) {
                DB::beginTransaction();

                if (request('picture')) {
                    if ($data->ttd_picture) {
                        Storage::delete($data->ttd_picture);
                    }
                    $picture = request()->file('picture')->store('ttd');
                } elseif ($data->ttd_picture) {
                    $picture = $data->ttd_picture;
                } else {
                    $picture = null;
                }

                try {

                    $user = auth()->user()->karyawan_id;
                    $id = Crypt::decryptString($id);

                    $document = Document::find($id);
                    $diajukanOleh = User::where('karyawan_id', $document->pengirim_diajukan_oleh)
                        ->first(['id', 'name', 'email']);
                    $karyawan = Karyawan::where('id', $user)->first();

                    $dataSignDiajukanPengirim = Document::where('id', $id)->where('pengirim_diajukan_oleh', $user)->first();
                    $dataSignDisetujuiPengirim = Document::where('id', $id)->where('pengirim_disetujui_oleh', $user)->first();
                    $dataSignApproval = DocumentApproval::where('document_id', $id)->where('karyawan_id', $user)->first();
                    $dataSignRecipient = DocumentRecipient::where('document_id', $id)->where('karyawan_id', $user)->first();

                    //Melakukan pengecekan data ada atau tidak
                    if ($dataSignDiajukanPengirim != null) {
                        $dataSignDiajukanPengirim->update([
                            'status_pengirim_diajukan'   => 1
                        ]);
                    } elseif ($dataSignDisetujuiPengirim != null) {
                        $dataSignDisetujuiPengirim->update([
                            'status_pengirim_disetujui'   => 1
                        ]);

                        // Send email to pembuat surat
                        Mail::to($diajukanOleh->email)->send(new DocumentSignMail($document, $karyawan));
                    } elseif ($dataSignApproval != null) {
                        DB::table('document_approval')
                            ->where('document_id', $id)
                            ->where('karyawan_id', $user)
                            ->update(['status_approval' => 1]);

                        // Send email to pembuat surat
                        Mail::to($diajukanOleh->email)->send(new DocumentSignMail($document, $karyawan));
                    } elseif ($dataSignRecipient != null) {
                        DB::table('document_recipient')
                            ->where('document_id', $id)
                            ->where('karyawan_id', $user)
                            ->update(['status_recipient' => 1]);

                        // Send email to pembuat surat
                        Mail::to($diajukanOleh->email)->send(new DocumentSignMail($document, $karyawan));
                    } else {
                        return response()->json([
                            'status'  => 404,
                            'message' => "Data not found!",
                        ], 404);
                    }

                    $data->update([
                        'ttd_picture'   => $picture,
                    ]);

                    DB::commit();

                    return response()->json([
                        'status'  => 200,
                        'message' => "Document has been signature..!",
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

    public function signature(Request $request, $id)
    {
        $data = Karyawan::find(auth()->user()->karyawan_id);

        $validator = Validator::make([
            'signature'    => $request->signature
        ], [
            'signature'    => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            if ($data) {
                DB::beginTransaction();

                $signatureData = $request->input('signature');

                //Membuat kondisi langsung mendelete gambar yang lama pada storage
                if (request('signature')) {
                    if ($data->ttd_picture) {
                        Storage::delete($data->ttd_picture);
                    }
                    $signatureFileName = 'signature_' . time() . '.png';
                    Storage::put('ttd/' . $signatureFileName, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $signatureData)));
                }
                //End Kondisi

                try {

                    $user = auth()->user()->karyawan_id;
                    $id = Crypt::decryptString($id);

                    $document = Document::find($id);
                    $diajukanOleh = User::where('karyawan_id', $document->created_by)
                        ->first(['id', 'name', 'email']);
                    $karyawan = Karyawan::where('id', $user)->first();

                    $dataSignDiajukanPengirim = Document::where('id', $id)->where('pengirim_diajukan_oleh', $user)->first();
                    $dataSignDisetujuiPengirim = Document::where('id', $id)->where('pengirim_disetujui_oleh', $user)->first();
                    $dataSignApproval = DocumentApproval::where('document_id', $id)->where('karyawan_id', $user)->first();
                    $dataSignRecipient = DocumentRecipient::where('document_id', $id)->where('karyawan_id', $user)->first();

                    //Melakukan pengecekan data ada atau tidak
                    if ($dataSignDiajukanPengirim != null) {
                        $dataSignDiajukanPengirim->update([
                            'status_pengirim_diajukan'   => 1
                        ]);
                    } elseif ($dataSignDisetujuiPengirim != null) {
                        $dataSignDisetujuiPengirim->update([
                            'status_pengirim_disetujui'   => 1
                        ]);

                        // Send email to pembuat surat
                        Mail::to($diajukanOleh->email)->send(new DocumentSignMail($document, $karyawan));
                    } elseif ($dataSignApproval != null) {
                        DB::table('document_approval')
                            ->where('document_id', $id)
                            ->where('karyawan_id', $user)
                            ->update(['status_approval' => 1]);

                        // Send email to pembuat surat
                        Mail::to($diajukanOleh->email)->send(new DocumentSignMail($document, $karyawan));
                    } elseif ($dataSignRecipient != null) {
                        DB::table('document_recipient')
                            ->where('document_id', $id)
                            ->where('karyawan_id', $user)
                            ->update(['status_recipient' => 1]);

                        // Send email to pembuat surat
                        Mail::to($diajukanOleh->email)->send(new DocumentSignMail($document, $karyawan));
                    } else {
                        return response()->json([
                            'status'  => 404,
                            'message' => "Data not found!",
                        ], 404);
                    }

                    $data->update([
                        'ttd_picture'   => 'ttd/' . $signatureFileName,
                    ]);

                    DB::commit();

                    return response()->json([
                        'status'  => 200,
                        'message' => "Document has been signature..!",
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

    public function pilihJenisDocument(Request $request)
    {
        $validator = Validator::make([
            'jenis_document' => $request->jenis_document
        ], [
            'jenis_document' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->messages(),
            ]);
        } else {
            $msJenis = Jenis::where('nama', $request->jenis_document)->first();
            $template = DocumentTemplate::where('jenis_id', $msJenis->id)->first();

            try {
                if ($template) {
                    return response()->json([
                        'status'    => 200,
                        'url'       => "{{ route('document.create') }}",
                    ], 200);
                } else {
                    return response()->json([
                        'status'  => 500,
                        'message' => 'Template untuk ' . $msJenis->nama . ' belum dibuat, silahkan buat template untuk jenis document tersebut',
                    ], 500);
                }
            } catch (Throwable $th) {
                return response()->json([
                    'status'  => 500,
                    'message' => $th->getMessage(),
                ], 500);
            }
        }
    }
}
