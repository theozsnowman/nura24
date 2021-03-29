<div class="modal fade custom-modal" tabindex="-1" role="dialog" aria-labelledby="add-account-tag" aria-hidden="true" id="add-account-tag">
	<div class="modal-dialog">
		<div class="modal-content">

			<form method="post">
				@csrf

				<div class="modal-header">
					<h5 class="modal-title" id="add-account-tag">{{ __('Add tag') }}</h5>
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">{{ __('Close') }}</span></button>
				</div>

				<div class="modal-body">

					<div class="row">
						<div class="col-lg-12">
							<div class="form-group">
								<label>{{ __('Tag') }}</label>
								<select class="form-control" name="tag_id" type="text" required>
									<option value="">- {{ __('select') }} -</option>
									@foreach($all_tags as $tag)
									<option value="{{ $tag->id }}">{{ $tag->tag }}</option>
									@endforeach
								</select>
							</div>
						</div>

					</div>

				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">{{ __('Add tag') }}</button>
				</div>

			</form>

		</div>
	</div>
</div>