@extends('master.admin.app')

@push('style')
<style>
    .ck-editor__editable[role="textbox"] {
        /* editing area */
        min-height: 500px;
    }
</style>
@endpush

@push('plugin-styles')
  <link href="{{ asset('assets/admin/plugins/select2/select2.min.css') }}" rel="stylesheet" />
@endpush

@section('content')
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Master</a></li>
        <li class="breadcrumb-item"><a href="{{ route('document.index') }}">{{ $breadcrumb }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah {{ $breadcrumb }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-12 stretch-card">
        <div class="card">
            <div class="card-header flex flex-align-center">
                <h6 class="card-title flex-full-width mb-0">Tambah {{ $breadcrumb }}</h6>
                <a href="{{ route('document.index') }}" type="button" class="btn btn-sm btn-secondary btn-icon-text">
                    <i class="btn-icon-prepend" data-feather="arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                @if(session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        {{ session()->get('error') }}
                    </div>
                    @php
                        Session::forget('error');
                    @endphp
                @endif
                <form action="{{ route('document.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="mb-3">
                                <label class="form-label">Nomor Surat <span class="text-danger">*</span></label>
                                <input name="no_surat" type="text" class="form-control @error('no_surat') is-invalid @enderror"
                                    placeholder="Masukkan nomor surat" value="{{ old('no_surat') }}" autofocus>
                                @error('no_surat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="mb-3">
                                <label class="form-label">Jenis Document <span class="text-danger">*</span></label>
                                <input type="hidden" name="jenis_document" class="@error('jenis_document') is-invalid @enderror" value="{{ $template->jenis_id }}">
                                <select class="js-example-basic-single form-select" data-width="100%" disabled>
                                    <option selected disabled>-- Pilih Jenis Document --</option>
                                    @foreach ($jenis as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('jenis_document', $template->jenis_id) == $item->id ? 'selected' : null }}>{{ $item->nama }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('jenis_document')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <hr>

                    <h6 class="mb-3">Pihak Pengirim</h6>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Diajukan Oleh <span class="text-danger">*</span></label>
                                <select name="diajukan_oleh" class="js-example-basic-single form-select @error('diajukan_oleh') is-invalid @enderror" data-width="100%">
                                    <option selected disabled>-- Pilih Karyawan --</option>
                                    @foreach ($karyawan as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('diajukan_oleh') == $item->id ? 'selected' : null }}>{{ $item->nama }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('diajukan_oleh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Disetujui Oleh <span class="text-danger">*</span></label>
                                <select name="disetujui_oleh" class="js-example-basic-single form-select @error('disetujui_oleh') is-invalid @enderror" data-width="100%">
                                    <option selected disabled>-- Pilih Karyawan --</option>
                                    @foreach ($karyawan as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('disetujui_oleh') == $item->id ? 'selected' : null }}>{{ $item->nama }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('disetujui_oleh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <hr>

                    <h6 class="mb-3">Pihak Penerima :</h6>
                    <div class="row form-row" id="recipient-form">
                        <div class="form-group col-12 col-md-10 mb-3">
                            <div class="table-responsive">
                                <table id="tb-recipient" class="table table-bordered" width="100%" cellspacing="0">
                                    <thead style="background-color:#9BB8CD">
                                        <tr>
                                            <th style="text-align: center; color:black">User Recipient</th>
                                            <th style="color: black">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (old('recipient'))
                                            @for ($i = 0; $i < count(old('recipient')); $i++)
                                                <tr>
                                                    <td>
                                                        <select class="js-example-basic form-select select-recipient @error('recipient.'.$i) is-invalid @enderror" name="recipient[]">
                                                            <option selected disabled>Select User Recipient</option>
                                                            @foreach ($karyawan as $item)
                                                            <option value="{{ $item->id }}" 
                                                                {{ old('recipient.'.$i) == $item->id ? 'selected' : null }}>{{ $item->nama }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @error('recipient.'.$i)
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td width="50px">
                                                        <button id="btn-recipient-delete" type="button" class="btn btn-danger">Delete</button>
                                                    </td>
                                                </tr>
                                            @endfor 
                                        @else
                                        <tr>
                                            <td>
                                                <select class="js-example-basic-multiple form-select @error('recipient') is-invalid @enderror" name="recipient[]" id="recipient">
                                                    <option selected disabled>Select User Recipient</option>
                                                    @foreach ($karyawan as $item)
                                                    <option value="{{ $item->id }}" 
                                                        {{ old('recipient') == $item->id ? 'selected' : null }}>{{ $item->nama }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('recipient')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td width="80px">
                                                <button id="btn-recipient-delete" type="button" class="btn btn-danger">Delete</button>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group col-12 col-md-1">
                            <button id="btn-recipient-add" type="button" class="btn btn-primary"><i data-feather="plus"></i></button>
                        </div>
                    </div>
                    <hr>
                    
                    <h6 class="mb-3">Disetujui Oleh :</h6>
                    <div class="row form-row" id="approval-form">
                        <div class="form-group col-12 col-md-10 mb-3">
                            <div class="table-responsive">
                                <table id="tb-approval" class="table table-bordered" width="100%" cellspacing="0">
                                    <thead style="background-color:#9BB8CD">
                                        <tr>
                                            <th style="text-align: center; color:black">User Approval</th>
                                            <th style="color: black">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (old('approval'))
                                            @for ($i = 0; $i < count(old('approval')); $i++)
                                                <tr>
                                                    <td>
                                                        <select class="js-example-basic-multiple form-select select-approval @error('approval.'.$i) is-invalid @enderror" name="approval[]">
                                                            <option selected disabled>Select User Recipient</option>
                                                            @foreach ($karyawan as $item)
                                                            <option value="{{ $item->id }}" 
                                                                {{ old('approval.'.$i) == $item->id ? 'selected' : null }}>{{ $item->nama }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                        @error('approval.'.$i)
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </td>
                                                    <td width="50px">
                                                        <button id="btn-approval-delete" type="button" class="btn btn-danger">Delete</button>
                                                    </td>
                                                </tr>
                                            @endfor 
                                        @else
                                        <tr>
                                            <td>
                                                <select class="js-example-basic-multiple form-select @error('approval') is-invalid @enderror" name="approval[]" id="approval">
                                                    <option selected disabled>Select User Approval</option>
                                                    @foreach ($karyawan as $item)
                                                    <option value="{{ $item->id }}" 
                                                        {{ old('approval') == $item->id ? 'selected' : null }}>{{ $item->nama }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('approval')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td width="80px">
                                                <button id="btn-approval-delete" type="button" class="btn btn-danger">Delete</button>
                                            </td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group col-12 col-md-1">
                            <button id="btn-approval-add" type="button" class="btn btn-primary"><i data-feather="plus"></i></button>
                        </div>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label class="form-label">Isi Document <span class="text-danger">*</span></label>
                                <textarea name="isi_document" id="textBody" class="form-control @error('isi_document') is-invalid @enderror" >{{ old('isi_document') ?? $template->template }}</textarea>
                                @error('isi_document')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="text-end">
                        <button name="btnCreateSimpan" class="btn btn-warning" type="submit" id="btnCreateSave">Simpan dan Tambah</button>
                        <button class="btn btn-warning" type="submit" id="btnCreateSave-loading" style="display: none">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            <span>Simpan dan Tambah</span>
                        </button>

                        <button name="btnSimpan" class="btn btn-primary" type="submit" id="btnSave">{{ $btnSubmit }}</button>
                        <button class="btn btn-primary" type="submit" id="btnSave-loading" style="display: none">
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            <span>{{ $btnSubmit }}</span>
                        </button>
                        <a href="{{ route('document.index') }}" class="btn btn-danger" id="btnCancel">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('plugin-scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/super-build/ckeditor.js"></script>
<script src="{{ asset('assets/admin/plugins/select2/select2.min.js') }}"></script>
@endpush

@push('custom-scripts')
<script src="{{ asset('assets/admin/js/select2.js') }}"></script>

<script>
    // This sample still does not showcase all CKEditor&nbsp;5 features (!)
    // Visit https://ckeditor.com/docs/ckeditor5/latest/features/index.html to browse all the features.
    CKEDITOR.ClassicEditor.create(document.getElementById("textBody"), {
        // https://ckeditor.com/docs/ckeditor5/latest/features/toolbar/toolbar.html#extended-toolbar-configuration-format
        toolbar: {
            items: [
                'selectAll', '|', 'heading', '|',
                'bold', 'italic', 'strikethrough', 'underline', 'subscript', 'superscript', 'removeFormat', '|',
                'bulletedList', 'numberedList', 'todoList', '|',
                'outdent', 'indent', '|',
                'undo', 'redo',
                '-',
                'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                'alignment'
            ],
            shouldNotGroupWhenFull: false
        },
        // Changing the language of the interface requires loading the language file using the <script> tag.
        // language: 'es',
        list: {
            properties: {
                styles: true,
                startIndex: true,
                reversed: true
            }
        },
        // https://ckeditor.com/docs/ckeditor5/latest/features/headings.html#configuration
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
            ]
        },
        // https://ckeditor.com/docs/ckeditor5/latest/features/editor-placeholder.html#using-the-editor-configuration
        placeholder: 'Masukkan isi dari document yang akan dibuat disini.... !',
        // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-family-feature
        fontFamily: {
            options: [
                'default',
                'Arial, Helvetica, sans-serif',
                'Courier New, Courier, monospace',
                'Georgia, serif',
                'Lucida Sans Unicode, Lucida Grande, sans-serif',
                'Tahoma, Geneva, sans-serif',
                'Times New Roman, Times, serif',
                'Trebuchet MS, Helvetica, sans-serif',
                'Verdana, Geneva, sans-serif',
                'Poppins'
            ],
            supportAllValues: true
        },
        // https://ckeditor.com/docs/ckeditor5/latest/features/font.html#configuring-the-font-size-feature
        fontSize: {
            options: [ 10, 12, 14, 'default', 18, 20, 22 ],
            supportAllValues: true
        },
        // The "super-build" contains more premium features that require additional configuration, disable them below.
        // Do not turn them on unless you read the documentation and know how to configure them and setup the editor.
        removePlugins: [
            // These two are commercial, but you can try them out without registering to a trial.
            // 'ExportPdf',
            // 'ExportWord',
            'CKBox',
            'CKFinder',
            'EasyImage',
            // This sample uses the Base64UploadAdapter to handle image uploads as it requires no configuration.
            // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/base64-upload-adapter.html
            // Storing images as Base64 is usually a very bad idea.
            // Replace it on production website with other solutions:
            // https://ckeditor.com/docs/ckeditor5/latest/features/images/image-upload/image-upload.html
            // 'Base64UploadAdapter',
            'RealTimeCollaborativeComments',
            'RealTimeCollaborativeTrackChanges',
            'RealTimeCollaborativeRevisionHistory',
            'PresenceList',
            'Comments',
            'TrackChanges',
            'TrackChangesData',
            'RevisionHistory',
            'Pagination',
            'WProofreader',
            // Careful, with the Mathtype plugin CKEditor will not load when loading this sample
            // from a local file system (file://) - load this site via HTTP server if you enable MathType.
            'MathType',
            // The following features are part of the Productivity Pack and require additional license.
            'SlashCommand',
            'Template',
            'DocumentOutline',
            'FormatPainter',
            'TableOfContents',
            'PasteFromOfficeEnhanced'
        ]
    });
</script>

<script type="text/javascript">
    let recipient = "<tr>"+
        "<td>"+
            "<select class='js-example-basic form-select select-recipient @error('recipient') is-invalid @enderror' name='recipient[]'>"+
                "<option value=''>Select User Recipient</option>"+
                "@foreach ($karyawan as $item)"+
                "<option value='{{ $item->id }}'>{{ $item->nama }}</option>"+
                "@endforeach"+
            "</select>"+
            "<p id='error-recipient' style='color: red' class='error'></p>"+
        "</td>"+
        "<td width='80px'>"+
            "<button id='btn-recipient-delete' type='button' class='btn btn-danger'>Delete</button>"+
        "</td>"+
    "</tr>"

    let approval = "<tr>"+
        "<td>"+
            "<select class='js-example-basic form-select select-approval @error('approval') is-invalid @enderror' name='approval[]'>"+
                "<option value=''>Select User Approval</option>"+
                "@foreach ($karyawan as $item)"+
                "<option value='{{ $item->id }}'>{{ $item->nama }}</option>"+
                "@endforeach"+
            "</select>"+
            "<p id='error-approval' style='color: red' class='error'></p>"+
        "</td>"+
        "<td width='80px'>"+
            "<button id='btn-approval-delete' type='button' class='btn btn-danger'>Delete</button>"+
        "</td>"+
    "</tr>"

    //--Repeat item form
    function selectRefresh() {
        $('.select-recipient').select2({
            tags: true,
            placeholder: "Select User Recipient",
            width: '100%'
        });

        $('.select-approval').select2({
            tags: true,
            placeholder: "Select User Approval",
            width: '100%'
        });
    }

    $(document).ready(function() {
        $('#btn-recipient-add').click(function() {
            $('#tb-recipient > tbody').append(recipient);
            selectRefresh();
        });

        $('tbody').on('click','#btn-recipient-delete', function() {
            $(this).parent().parent().remove();
        });

        $('#btn-approval-add').click(function() {
            $('#tb-approval > tbody').append(approval);
            selectRefresh();
        });

        $('tbody').on('click','#btn-approval-delete', function() {
            $(this).parent().parent().remove();
        });
    });
    //--- End repeat item form

    //Toast for session success
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    @if (session('success')) {
        Toast.fire({
            icon: 'success',
            title: "{{ session('success') }}",
        });
    }
    @endif

    //environment button
    $('#btnCreateSave').on('click', function () {
        $('#btnCreateSave-loading').toggle();
        $('#btnCreateSave-loading').prop('disabled',true);
        $('#btnCreateSave').toggle();
        $('#btnSave').toggle();
        $('#btnCancel').toggle();
    });

    $('#btnSave').on('click', function () {
        $('#btnSave-loading').toggle();
        $('#btnSave-loading').prop('disabled',true);
        $('#btnCreateSave').toggle();
        $('#btnSave').toggle();
        $('#btnCancel').toggle();
    });
    //end
</script>
@endpush