<div class="modal fade" id="modal-upload-sign" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg" >
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="title-modal-sign"></h4>
            </div>

            <form id="upload-picture-form" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <div class="row form-row">
                        <div class="col-12 col-sm-12">
                            <div class="mb-3">
                                <div class="form-group">
                                    <label>Upload Signature File <span class="text-danger">(Max: 1Mb, Format: JPG,JPEG,PNG) *</span></label>
                                    <input name="picture" id="picture-upload" type="file" class="form-control" accept="image/*" onchange="previewImages()">
                                    <p id="error-picture" style="color: red" class="error"></p>
                                </div>
                            </div>
                        </div>
                        <div id="preview"></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">
                            <span id="submit-loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="display: none;"></span>
                            <span id="btn-submit-text">Save</span>
                        </button>
                        <button class="btn btn-danger btn-cancel-upload-picture" type="button">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>