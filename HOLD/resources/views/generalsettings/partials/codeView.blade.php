{{-- keyword start with edit --}}



<div id="codeView-modal" class="modal fixed-left fade" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-dialog-aside" role="document" style="width: 30%;">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">Site Code</h5>

                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <div class="modal-body">


                <div class="row">
                    <div class="col-12">
                        <label for="">Booking Form</label>
                        <div class="snippet-clipboard-content notranslate position-relative overflow-auto row">
                            <pre class="notranslate col-11" id="iframeCode"></pre>
                            <div class="zeroclipboard-container col-1" style="cursor: pointer;"
                                onclick="copyCode('iframeCode')">
                                <i class="fas fa-copy"></i>
                            </div>
                        </div>
                    </div>


                    <!-- 
                    <div class="col-12">

                        <textarea id="iframeCode" readonly style="resize: unset;width:100%">

        </textarea>



                        <button class="copy-button" style="border: none;" onclick="copyCode('iframeCode')">
                            <i class="fas fa-copy"></i>
                        </button>


                    </div> -->
                </div>

            </div>

            <!-- <div class="modal-footer">

                
            </div> -->

        </div>

    </div> <!-- modal-bialog .// -->

</div> <!-- modal.// -->

<script>
    const copyCode = (id) => {
        try {
            let text = document.getElementById(id).textContent || document.getElementById(id).innerText;
            navigator.clipboard.writeText(text).then(() => {
                console.log('Text successfully copied!');
            }).catch((e) => {
                console.log(`Error copying text: ${e.message}`);
            });
        } catch (e) {
            console.log(`Error: ${e.message}`);
        }
    }
</script>