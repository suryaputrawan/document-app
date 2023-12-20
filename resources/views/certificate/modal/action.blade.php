<div class="modal fade" id="modal-create-certificate" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xl" >
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="title-modal"></h4>
            </div>

            <form id="certificate-form" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Certificate Number <span class="text-danger">*</span></label>
                                <input name="certificate_number" id="certificate-number" type="text" class="form-control"
                                    placeholder="Insert certificate number" value="{{ old('certificate_number') }}">
                                <p id="error-certificate-number" style="color: red" class="error"></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label">Type <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select" data-width="100%">
                                        <option value="">Select Certificate Type</option>
                                        @foreach ($types as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('type') == $item->id ? 'selected' : null }}>{{ $item->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <p id="error-type" style="color: red" class="error"></p>
                                </div>
                            </div> 
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-3">
                                <label class="form-label">Certificate Name <span class="text-danger">*</span></label>
                                <input name="name" id="name" type="text" class="form-control"
                                    placeholder="Insert certificate name" value="{{ old('name') }}">
                                <p id="error-name" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input name="start_date" id="start-date" type="date" class="form-control"
                                   value="{{ old('start-date') }}">
                                <p id="error-start-date" style="color: red" class="error"></p>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                <input name="end_date" id="end-date" type="date" class="form-control"
                                   value="{{ old('end_date') }}">
                                <p id="error-end-date" style="color: red" class="error"></p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Employee Name <span class="text-danger">*</span></label>
                                <input name="employee" id="employee" type="text" class="form-control"
                                placeholder="Insert name certificate owner" value="{{ old('employee') }}">
                                <p id="error-employee" style="color: red" class="error"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label class="form-label">Hospital / Clinic <span class="text-danger">*</span></label>
                                    <select name="hospital" id="hospital" class="form-select" data-width="100%">
                                        <option value="">Select Hospital / Clinic</option>
                                        @foreach ($hospital as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('hospital') == $item->id ? 'selected' : null }}>{{ $item->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <p id="error-hospital" style="color: red" class="error"></p>
                                </div>
                            </div> 
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label class="form-label">Upload Cerfiticate File <span class="text-danger">*</span></label>
                                <input name="file" id="file" type="file" class="form-control" accept="image/*,.pdf" onchange="previewImages()">
                                <p id="error-file" style="color: red" class="error"></p>
                                <code class="text-danger" style="font-size: 8pt">File Max: 1Mb. Format: jpg, jpeg, png, pdf</code>
                            </div>
                            <div id="preview"></div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">
                            <span id="submit-loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="display: none;"></span>
                            <span id="btn-submit-text">Save</span>
                        </button>
                        <button class="btn btn-danger btn-cancel" type="button">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>