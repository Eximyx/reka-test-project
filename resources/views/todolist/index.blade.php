@extends('layouts.datatable')

@section('datatable')
    <div class="container">
        <div class="row">
            <button class="btn btn-success" onclick="add()">Добавить</button>
        </div>
        <div class="row">
            {!! $dataTable->table() !!}
        </div>
    </div>
@endsection

@section('form')
    <div id="validation-errors" class="mb-2"></div>
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label class="form-label" for="title">{{__('models.form.todolist.title')}}</label>
                <input type="text" class="form-control" name="title" id="title" required>
            </div>
            {{--            <div class="form-group">--}}
            {{--                <label class="form-label" for="image">Загрузить изображение</label>--}}
            {{--                <div class="form-control-wrap">--}}
            {{--                    <div class="form-file">--}}
            {{--                        <input type="file" name="image" multiple="" class="form-control" id="image"--}}
            {{--                               accept=".jpg, .jpeg, .png">--}}
            {{--                    </div>--}}
            {{--                    <div id="img-div" class="form-control" hidden>--}}
            {{--                        <img id="img-preview" class="img-thumbnail" src="" alt="">--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--            </div>--}}
        </div>
        {{--        <div class="col-md-6">--}}
        {{--            <div class="form-group">--}}
        {{--                <label class="form-label" for="description">Описание</label>--}}
        {{--                <div class="form-control-wrap">--}}
        {{--                    <textarea class="form-control" name="description" id="description"--}}
        {{--                              required rows="3"></textarea>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}
        {{--        <div class="col-md-12">--}}
        {{--            <div class="form-group">--}}
        {{--                <label class="form-label" for="content">Содержимое</label>--}}
        {{--                <textarea class="form-control" name="content" id="content"--}}
        {{--                          required rows="3"></textarea>--}}
        {{--            </div>--}}
        {{--        </div>--}}
        <div class="row justify-content-end mt-2">
            <div class="col-md-auto">
                <button id="btn-save" type="submit" class="btn btn-primary">
                    Подтвердить
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
    <script type="text/javascript">
        const urls = "{{ request()->url() }}";

        function add() {
            $('#form')[0].reset();
            $('#modal-form').modal("show");
            $('#modal-title').text("Добавление")
            $('#id').val('');
        }

        function editFunc(id) {
            $("#validation-errors").html("")
            $("#errors").html("")

            $.ajax({
                type: "GET",
                url: urls + `/${id}` + '/edit',
                dataType: 'json',
                success: function (res) {
                    console.log(res);

                    let data = res['data'];

                    let entity = data["entity"];

                    if (res['success']) {
                        $('#form')[0].reset();
                        $('#modal-title').text("Редактирование");
                        $('#modal-form').modal("show");

                        $.each(entity, function (key, value) {
                                $('#' + key).val(value);
                            }
                        )
                    } else {
                        $("#errors").append("<li class='alert alert-danger'>" + res.data.message + "</li>")
                        setTimeout(() => {
                            $("#errors").html("")
                        }, 5000)
                    }
                },
                error: function (data) {
                    console.log(data);
                    if (data.status === 422) {
                        $("#validation-errors").append("<li class='alert alert-danger'>" + data.responseJSON.message + "</li>")
                        setTimeout(() => {
                            $("#validation-errors").html("")
                        }, 5000)
                    } else {
                        $("#errors").append("<li class='alert alert-danger'>" + data.responseJSON.message + "</li>")
                        setTimeout(() => {
                            $("#errors").html("")
                        }, 5000)
                    }
                },
            });
        }

        function deleteFunc(id) {
            var id = id;

            $("#success").html("");
            $("#errors").html("")

            $.ajax({
                type: "DELETE",
                url: urls + `/delete/${id}`,
                dataType: 'json',
                success: function (data) {
                    var oTable = $('#entity-table').DataTable();
                    oTable.draw(false);
                    $("#success").append("<li class='alert alert-success'>" + data.data.message);
                    setTimeout(() => {
                        $("#success").html("")
                    }, 5000)
                },
                error: function (data) {
                    console.log(data);
                    $("#errors").append("<li class='alert alert-warning'>" + data.responseJSON.message + "</li>")
                    setTimeout(() => {
                        $("#errors").html("")
                    }, 5000)
                },
            });
        }

        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#form').submit(function (e) {
                $("#success").html("")
                $("#errors").html("")
                $("#validation-errors").html("")

                e.preventDefault();
                let formData = new FormData(this);
                let url = '';
                let type = 'POST';

                let id = $('#id').val();

                if (id !== '') {
                    url += `/${id}?_method=PATCH`;
                }
                $.ajax({
                    type: type,
                    url: urls + url,
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: (data) => {
                        console.log(data);

                        $("#modal-form").modal('hide');
                        var oTable = $('#entity-table').DataTable();
                        oTable.draw(false);
                        $("#form")[0].reset();


                        if (data['success']) {
                            $("#success").append("<li class='alert alert-success'>" + data.data.message);
                            setTimeout(() => {
                                $("#success").html("")
                            }, 5000)
                        } else {
                            $("#errors").append("<li class='alert alert-danger'>" + data.data.message);
                            setTimeout(() => {
                                $("#errors").html("")
                            }, 5000)
                        }
                    },
                    error: function (data) {
                        console.log(data);

                        if (data.status === 422) {
                            $("#validation-errors").append("<li class='alert alert-warning'>" + data.responseJSON.message + "</li>")
                            setTimeout(() => {
                                $("#validation-errors").html("")
                            }, 5000)
                        } else {
                            $("#errors").append("<li class='alert alert-danger'>" + data.responseJSON.message + "</li>")
                            setTimeout(() => {
                                $("#errors").html("")
                            }, 5000)
                        }
                    },
                });
            });
        });
    </script>
@endpush