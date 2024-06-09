@extends('layouts.datatable')

@section('datatable')
    <div class="container">
        <div class="row">
            <button class="btn btn-success" onclick="add()">Добавить</button>
            <button class="btn btn-secondary mt-2" onclick="createTag()">Создать тег</button>
        </div>

        <div class="row">
            {!! $dataTable->table() !!}
        </div>
    </div>
    <div class="modal fade" id="tag-modal" tabindex="-1" aria-labelledby="tagModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tagModalLabel">Создать тег</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="tag-form" action="javascript:void(0)" class="mt-2" enctype="multipart/form-data">
                        <div id="tag-validation-errors" class="mb-2"></div>
                        <div class="form-group">
                            <label class="form-label" for="tag-title">Название тега</label>
                            <input type="text" class="form-control" id="tag-title">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="button" class="btn btn-primary" onclick="saveTag()">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('form')
    <div id="validation-errors" class="mb-2"></div>
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label class="form-label" for="title">{{__('models.form.todolist.title')}}</label>
                <input type="text" class="form-control" name="title" id="title">
            </div>
            <div class="form-group">
                <label class="form-label" for="tags">Теги</label>
                <select class="form-control" name="tags[]" id="tags" multiple></select>
            </div>
            <div class="form-group">
                <label class="form-label" for="image">Загрузить изображение</label>
                <div class="form-control-wrap">
                    <div class="form-file">
                        <input type="file" name="image" multiple="" class="form-control" id="image"
                               accept=".jpg, .jpeg, .png">
                    </div>
                    <div id="img-div" class="form-control" hidden>
                        <img id="img-preview" class="img-thumbnail" src="" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-end mt-2">
            <div class="col-md-auto">
                <button id="btn-save" type="submit" class="btn btn-primary">
                    Подтвердить
                </button>
            </div>
        </div>
    </div>

    <!-- Modal for creating a new tag -->
@endsection

@push('scripts')
    {!! $dataTable->scripts(attributes: ['type' => 'module']) !!}
    <script type="text/javascript">
        const urls = "{{ request()->url() }}";
        loadTags();

        function add() {
            $('#form')[0].reset();
            $('#modal-form').modal("show");
            $('#modal-title').text("Добавление")
            $('#id').val('');
            $("#image").val(null);
        }

        function createTag() {
            $('#tag-modal').modal('show');
            $('#tag-form')[0].reset();
        }

        function saveTag() {
            const tagTitle = $('#tag-title').val();

            $.ajax({
                type: 'POST',
                url: '{{ route('tags.store') }}',
                data: {title: tagTitle},
                success: function (response) {
                    if (response.success) {
                        $('#tag-modal').modal('hide');
                        loadTags();
                    } else {
                        $('#tag-validation-errors').html('<li class="alert alert-danger">' + response.message + '</li>');
                    }
                },
                error: function (response) {
                    $('#tag-validation-errors').html('<li class="alert alert-danger">' + response.responseJSON.message + '</li>');
                }
            });
        }

        function loadTags() {
            $.ajax({
                type: 'GET',
                url: '{{ route('tags.index') }}',
                success: function (response) {
                    if (response.success) {
                        const tags = response.tags;
                        $('#tags').empty();
                        tags.forEach(tag => {
                            $('#tags').append(new Option(tag.title, tag.id));
                        });
                    }
                }
            });
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
                        const tags = data.tags.map(tag => tag.id);
                        $('#tags').val(tags).trigger('change');
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

            $('#tags').select();

            loadTags();

            $('#image').on("change", (e) => {
                console.log(e.target.files[0]);
                if (!previewFunc(e.target.files[0])) {
                    $('#image').val(null);
                    $('#img-div').attr('hidden', true);
                    $('#img-preview').attr("src", null);
                }
            });

            function previewFunc(file) {
                if (file === undefined || !file.type.match(/image.*/)) {
                    return false
                }
                const reader = new FileReader();
                reader.addEventListener("load", (e) => {
                    $('#img-preview').attr("src", e.target.result);
                });
                $('#img-div').attr('hidden', false);
                reader.readAsDataURL(file);
                return true;
            }

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
