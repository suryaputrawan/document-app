<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Models\Jenis;
use App\Models\Document;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\DocumentMail;
use App\Mail\DocumentSignMail;
use App\Models\DocumentApproval;
use App\Models\DocumentRecipient;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;

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
                            if ($user->can('edit document')) {
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
                    $signRoute      = 'document.sign';
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
                            <button class="btn btn-success sign-document" 
                            data-label="' . $dataLabel . '" data-url="' . route($signRoute, $dataId) . '">
                                Sign Doc
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
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('surat.create', [
            'breadcrumb'    => 'Document',
            'btnSubmit'     => 'Simpan',
            'jenis'         => Jenis::get(['id', 'nama']),
            'karyawan'      => Karyawan::orderBy('nama', 'asc')->get(['id', 'nama', 'jabatan'])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_surat'          => 'required|max:255|min:5|unique:documents,no_surat',
            'jenis_document'    => 'required',
            'diajukan_oleh'     => 'required',
            'disetujui_oleh'    => 'required',
            'approval'          => 'array',
            'isi_document'      => 'required|min:5',
        ]);

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
        return $pdf->setPaper('a4', 'portrait')->stream();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $id = Crypt::decryptString($id);
            $data = Document::find($id);

            if (!$data) {
                return redirect()
                    ->back()
                    ->with('error', "Data not found..");
            }

            return view('surat.edit', [
                'breadcrumb'    => 'Document',
                'btnSubmit'     => 'Simpan Perubahan',
                'data'          => $data,
                'jenis'         => Jenis::get(['id', 'nama']),
                'karyawan'      => Karyawan::orderBy('nama', 'asc')->get(['id', 'nama', 'jabatan'])
            ]);
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', "Error on line {$e->getLine()}: {$e->getMessage()}");
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $id = Crypt::decryptString($id);
        $data = Document::find($id);

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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
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
    }

    public function sign($id)
    {
        DB::beginTransaction();
        try {
            //Mempersiapkan data
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

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => "Document has been signature..!",
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 500,
                'message' => "Error on line {$e->getLine()}: {$e->getMessage()}",
            ], 500);
        }
    }
}
