<!-- Modal-->
<div class="modal fade " id="popupModal" tabindex="-1" role="dialog" aria-labelledby="popupModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="/" class="modalForm" id="modalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="popupModalTitle">Modal Title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="popupModalValueId" value="" />
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="modalValueCallback" value="" />
                    <p class="modal-text" id="popupModalText"></p>
                </div>
                <div class="modal-footer">
                    <div class="modal-add-button">
                        <button type="button" class="btn btn-primary font-weight-bold modalAction" id="modalAction">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>