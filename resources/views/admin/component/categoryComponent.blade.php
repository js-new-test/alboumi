<div class="modal mt-5 fade" id="categoryComponentModel" tabindex="-1" role="dialog" aria-labelledby="userIsActiveModelLabel">
    <div class="modal-dialog modal-lg" role="document" style="max-height: 500px; overflow-y: scroll;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Category</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="col-md-10 mx-auto">
                    <input type="hidden" name="categoryCounterValue" id="categoryCounterValue" readonly="true">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="category_ids">Category</label>
                                <select class="select2 form-control multiselect-dropdown"
                                    name="categoryIds[]" id="categoryIds" multiple>
                                </select>
                            </div>
                        </div>
                    </div>

                </form>
                
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" id="saveCategory">Save</button>
                <button class="btn btn-default" data-dismiss="modal" aria-label="Close">Colse</button>
            </div>
        </div>
    </div>
</div>
