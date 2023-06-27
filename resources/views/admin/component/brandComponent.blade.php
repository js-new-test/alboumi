<div class="modal mt-5 fade" id="brandComponentModel" tabindex="-1" role="dialog" aria-labelledby="userIsActiveModelLabel">
	<div class="modal-dialog modal-lg" role="document" style="height: 500px; overflow-y: scroll;">
		<div class="modal-content">
			<div class="modal-header">
				<h3>Brand</h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
				<input type="hidden" name="brandCounterValue" id="brandCounterValue" readonly="true">
			<div class="modal-body">

				<div class="main-card mb-3 card">
					<div class="card-body">
						<table id="tableManufacturers" class="table table-hover table-striped table-bordered" width="100%">
							<thead>
								<tr>
									<th><input type="checkbox" name="brandCheckAll" id="brandCheckAll"></th>
									<th width="30%">Name</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>

						<div class="float-right mt-3">
							<button type="button" class="btn btn-primary" id="saveBrands">Save</button>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
