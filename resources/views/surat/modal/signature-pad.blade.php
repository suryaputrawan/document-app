<div class="modal fade" id="modal-signature-pad" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Signature Document Here</h5>
            </div>
            <form id="signature-form" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <canvas id="signaturePad" width="400" height="200"></canvas>
                    <input type="hidden" name="signature" id="signatureInput">
                    <p id="error-signature" style="color: red" class="error"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="clear-signature" class="btn btn-warning me-5" onclick="clearSignature()">Clear Signature</button>
                    <button class="btn btn-primary" type="submit">
                        <span id="submit-signature-loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="display: none;"></span>
                        <span id="btn-submit-signature">Save</span>
                    </button>
                    <button class="btn btn-danger btn-cancel-signature" type="button">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>