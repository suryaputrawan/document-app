<div class="modal fade" id="modal-pilih-jenis" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-xs" >
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Pilih Jenis Document</h4>
            </div>

            <form id="pilih-jenis-form" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-sm-12">
                            <div class="mb-3">
                                <div class="form-group">
                                    <select name="jenis_document" id="jenis-document" class="form-select" data-width="100%">
                                        <option value="">Pilih Jenis Document</option>
                                        @foreach ($jenis as $item)
                                        <option value="{{ $item->nama }}"
                                            {{ old('jenis_document') == $item->id ? 'selected' : null }}>{{ $item->nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <p id="error-jenis-document" style="color: red" class="error"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">
                            <span id="submit-jenis-loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="display: none;"></span>
                            <span id="btn-submit-jenis">Save</span>
                        </button>
                        <button class="btn btn-danger btn-cancel-jenis" type="button">Batal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>