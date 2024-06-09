@extends('layouts.app')

@section('content')
    <div class="container ">
        <div class="row justify-content-center">
            <div id="success" class="mb-2"></div>
            <div id="errors" class="mb-2"></div>
            <div class="col-md-8">
                @yield('datatable')
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-form">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div id="errors"></div>
                <div class="modal-body modal-body-md">
                    <div class="row justify-content-between">
                        <div class="col-auto">
                            <h5 id="modal-title" class="modal-title">{{__('models.form.add')}}</h5>
                        </div>
                        <div class="col-auto">
                            <a href="" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <em class="fa fa-close text-black"></em>
                            </a>
                        </div>
                    </div>
                    <form id="form" action="javascript:void(0)" class="mt-2" enctype="multipart/form-data">
                        <input type="text" id="id" name="id" hidden>
                        <input type="text" id="user_id" name="user_id" hidden value="{{Auth::user()->id}}">
                        @yield('form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
@endpush