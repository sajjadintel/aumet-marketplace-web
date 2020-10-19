<div class="modal fade " id="webGuidedTourModal" tabindex="-1" role="dialog" aria-labelledby="popupModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tour</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Something awesome has changed in this site. Start tour to get a guided introduction.
                    </p>
                </div>
                <div class="modal-footer">
                    <div class="modal-add-button">

                        <button class="btn btn-light" data-dismiss="modal">No Thanks</button>
                        <button autofocus="autofocus" class="btn btn-primary font-weight-bold" data-dismiss="modal" id="webGuidedTourModalStart" onclick="WebGuidedTour.start()">Start Tour</button>
                    </div>
                </div>
        </div>
    </div>
</div>

<div style="display: none" id="tour-step1-info">
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis, earum!
    </p>
</div>
<div style="display: none" id="tour-step2-info">
    <p>
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ullam excepturi, porro ipsa magnam, consequuntur quia.
    </p>
</div>