@extends('layouts.front')
@section('content')


<section class="user-dashbord">
    <div class="container">
      <div class="row">
        @include('includes.user-dashboard-sidebar')
        <div class="col-lg-8">
					<div class="user-profile-details">
						<div class="order-history">
							<div class="header-area">
								<h4 class="title">
									{{ $langg->lang277 }}
								</h4>
							</div>
							<div class="mr-table allproduct mt-4">
									<div class="table-responsiv">
											<table id="example" class="table table-hover dt-responsive" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>{{ $langg->lang278 }}</th>
														<th>{{ $langg->lang279 }}</th>
														<th>{{ $langg->lang280 }}</th>
														<th>{{ $langg->lang281 }}</th>
														<th>Причина отмена заказа</th>
														<th>{{ $langg->lang282 }}</th>
													</tr>
												</thead>
												<tbody>
													 @foreach($orders as $order)
													<tr>
														<td>
																{{$order->order_number}}
														</td>
														<td>
																{{date('d M Y',strtotime($order->created_at))}}
														</td>
														<td>
																{{ $order->pay_amount }} {{$order->currency_sign}}
														</td>
														<td>
															<div class="order-status {{ $order->status }}">
																	{{ucwords($order->status)}}
															</div>
														</td>
														<td>
															{{$order->reason_cancel}}
														</td>
														<td>

															<div class="btn-group">
																<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	Actions
																</button>
																<div class="dropdown-menu">
																	<a class="dropdown-item btn-sm" href="{{route('user-order',$order->id)}}">
																		{{ $langg->lang283 }}
																	</a>
																	<div class="dropdown-divider"></div>
																	@if($order->status == 'pending' || $order->status == 'processing' )
																		<button class="dropdown-item userOrderCancel btn-sm" href="" data-url="{{route('user-order-cancel',$order->id)}}" >
																			Отмена заказа
																		</button>
																	@else
																		<button class="dropdown-item userOrderCancel btn-sm" href="" data-url="{{route('user-order-cancel',$order->id)}}" disabled>
																			Отмена заказа
																		</button>
																	@endif
																</div>
															</div>
															{{--<a href="{{route('user-order',$order->id)}}">
																	{{ $langg->lang283 }}
															</a>--}}
														</td>
													</tr>
													@endforeach
												</tbody>
											</table>
									</div>

								{{--Model User Order Cancel--}}
								<div class="modal fade" id="userOrderCancelModal" tabindex="-1" role="dialog" aria-labelledby="userOrderCancelModal" aria-hidden="true">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="exampleModalLabel">Отмена заказа</h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<!-- Modal body -->
											<div class="modal-body">
												<textarea class="form-control" name="reasonCancel"  rows="2" cols=50  placeholder="Напишите причину..."></textarea>
											</div>
											<!-- Modal footer -->
											<div class="modal-footer justify-content-center">
												<button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __("Отмена") }}</button>
												<button class="btn btnCancelOrder btn-primary" data-dismiss="modal">Ок</button>
											</div>
										</div>
									</div>
								</div>
								{{--End Model User Order Cancel--}}

								</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection
@section('scripts')
	<script>
		// User order cancel Start
		let linkCancel=null;
		$(".userOrderCancel").click(function (e) {
			//e.preventDefault();
			linkCancel = $(this).attr('data-url');
			$('#userOrderCancelModal').modal('show');

		});

		$('#userOrderCancelModal .btnCancelOrder').click(function (e) {
			let resCancel = $('#userOrderCancelModal textarea[name="reasonCancel"]').val();
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				method: 'POST',
				url: linkCancel,
				data: {reasonCancel: resCancel},
				success: function () {
					location.reload();
				}
			});
		});

	</script>
@endsection