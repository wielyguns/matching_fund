@extends('../layouts/cms/main_cms')
@section('body_main')
    <!-- end page title end breadcrumb -->
    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="btn-group float-right">
                    <ol class="breadcrumb hide-phone p-0 m-0">
                        <li class="breadcrumb-item"><a href="#">{{ convertSlug(Request::segment(1)) }}</a></li>
                        <li class="breadcrumb-item"><a href="#">{{ convertSlug(Request::segment(2)) }}</a></li>
                        <li class="breadcrumb-item active">{{ convertSlug(Request::segment(3)) }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ convertSlug(Request::segment(3)) }}</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary d-flex justify-content-between">
                    <h4 class="header-title text-light">Index</h4>
                    <button class="btn btn-info" id="tambah-data" onclick="refreshState('#modal-create-data',true)"><i
                            class="fas fa-plus"></i>
                        Tambah Data</button>
                </div>
                <div class="card-body">
                    <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap table-hover"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <th>No</th>
                            <th>Aksi</th>
                            <th>Title</th>
                            <th>Created At</th>
                            <th>Status</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->

    <div class="modal fade bs-example-modal-md" id="modal-create-data" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0" id="myLargeModalLabel">Create Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form class="modal-body" id="form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="title" class="col-form-label">Title {{ dot() }}</label>
                                <input class="form-control required" type="text" placeholder="Masukan nama title"
                                    id="title" name="title">
                                <input type="hidden" id="id" name="id">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <label for="title" class="col-form-label">Content {{ dot() }}</label>
                            <div id="summernote"></div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="store()">Simpan
                        Data</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('script')
    <script>
        var table;
        var mask;
        $(document).ready(function() {
            table = $('#datatable-buttons').DataTable({
                processing: true,
                serverSide: true,
                responsive: {
                    details: {
                        renderer: function(api, rowIdx, columns) {
                            var data = $.map(columns, function(col, i) {
                                return col.hidden ?
                                    '<tr data-dt-row="' + col.rowIndex + '" data-dt-column="' +
                                    col.columnIndex + '">' +
                                    '<td>' + col.title + '</td> ' +
                                    '<td>' + col.data + '</td>' +
                                    '</tr>' :
                                    '';
                            }).join('');

                            return data ? $('<table style="width:100%"/>').append(data) : false;
                        }
                    }
                },
                buttons: [
                    'copy',
                    'excel',
                    'pdf',
                    {
                        extend: 'colvis',
                        prefixButtons: [{
                            extend: 'colvisRestore',
                            className: 'custom-html-collection',
                        }],
                        columnText: function(dt, idx, title) {

                            return (idx + 1) + ': ' + title;
                        }
                    }
                ],
                ajax: {
                    url: "{{ route('datatable-faq') }}",
                    data: {
                        _token: '{{ csrf_token() }}'
                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    class: 'text-center'
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    class: 'text-center',
                }, {
                    data: 'title',
                    name: 'title',
                }, {
                    data: 'created_at',
                    name: 'created_at',
                }, {
                    data: 'status',
                    class: 'text-center',
                }, ]
            });

            table.buttons().container()
                .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');

            $('.select2').select2({
                dropdownParent: $("#modal-create-data .modal-content"),
                theme: 'bootstrap4',
                width: '100%'
            });

            $('.mask').maskMoney({
                precision: 0,
                thousands: ',',
                allowZero: true,
                defaultZero: true,
            })

            $('#summernote').summernote({
                height: 300, // set editor height
                minHeight: null, // set minimum height of editor
                maxHeight: null, // set maximum height of editor
                focus: true // set focus to editable area after initializing summernote
            });
        })

        $('#tambah-data').click(function() {
            $('#modal-create-data').modal('toggle')
            $('#summernote').summernote('code', '');
        })

        $('.dropify').dropify({
            messages: {
                'default': 'Drag and drop a file here or click',
                'replace': 'Drag and drop or click to replace',
                'remove': 'Remove',
                'error': 'Ooops, something wrong happended.'
            }
        });

        function gantiStatus(param, id) {
            $.ajax({
                url: '{{ route('ganti-status-faq') }}',
                data: {
                    id,
                    param
                },
                type: 'get',
                success: function(data) {
                    table.ajax.reload(null, false);
                    alertify.logPosition("top right");
                    alertify.success(data.message);
                },
                error: function(data) {
                    gantiStatus(param, id);
                }
            });
        }



        function edit(id) {
            $.ajax({
                url: '{{ route('edit-faq') }}',
                data: {
                    id,
                },
                type: 'get',
                success: function(data) {
                    var temp_key = Object.keys(data.data);
                    var temp_value = data.data;
                    for (var i = 0; i < temp_key.length; i++) {
                        var key = temp_key[i];
                        if ($('#' + key).length != 0) {
                            if (!$('#' + key).hasClass('dropify')) {
                                $('#' + key).val(temp_value[key]);
                            }
                        }
                    }

                    $('#summernote').summernote('code', data.data.content);

                    $('.not-editable').prop('readonly', true);
                    $('#modal-create-data .select2').trigger('change.select2');
                    $('#simpan').removeClass('hidden');
                    $('#modal-create-data').modal('toggle')
                    $('.mask').maskMoney('mask');
                },
                error: function(data) {
                    gantiStatus(param, id);
                }
            });
        }

        function convertToSlug(str) {

            //replace all special characters | symbols with a space
            str = str.replace(/[`~!@#$%^&*()_\-+=\[\]{};:'"\\|\/,.<>?\s]/g, ' ')
                .toLowerCase();

            // trim spaces at start and end of string
            str = str.replace(/^\s+|\s+$/gm, '');

            // replace space with dash/hyphen
            str = str.replace(/\s+/g, '-');

            return str;
        }

        function store() {
            var validation = 0;
            $('#form-data .required').each(function() {
                var par = $(this).parents('.form-group');
                if ($(this).val() == '' || $(this).val() == null) {
                    console.log($(this));
                    $(this).addClass('is-invalid');
                    validation++
                }
            })

            if (validation != 0) {
                alertify.logPosition("top right");
                alertify.error("Semua data harus diisi");
                return false;
            }

            var formData = new FormData();

            var data = $('#form-data').serializeArray();
            data.forEach((d, i) => {
                formData.append(d.name, d.value);
            })

            formData.append('content', $('#summernote').summernote('code'));

            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: 'Proses Aksi Ini?',
                text: "Proses ini tidak bisa dikembalikan!",
                allowEnterKey: true,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya lanjutkan!',
                cancelButtonText: 'Tidak!',
                showLoaderOnConfirm: true,
            }).then((result) => {
                // $('.swal2-confirm').focus();
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);
                    $.ajax({
                        url: '{{ route('store-faq') }}',
                        data: formData,
                        type: 'post',
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: 'Success',
                                    text: data.message,
                                    icon: "success",
                                }).then(() => {
                                    clear('#modal-create-data', true);
                                    $('.dropify-clear').click();
                                    $('#summernote').summernote('code', '');
                                })
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: 'Oops Something Wrong',
                                    html: data.message,
                                    icon: "warning",
                                });
                            } else {
                                Swal.fire({
                                    title: 'Oops Something Wrong',
                                    html: data,
                                    icon: "warning",
                                });
                            }
                            overlay(false);
                            table.ajax.reload(null, false);
                        },
                        error: function(data) {
                            overlay(false);
                            var html = '';
                            var type = '';
                            if (data.responseJSON == undefined) {
                                type = 'text';
                                html = data.responseText;
                            } else {
                                type = 'json';
                                Object.keys(data.responseJSON).forEach(element => {
                                    html += data.responseJSON[element][0] + '<br>';
                                });
                            }

                            Swal.fire({
                                title: 'Oops Something Wrong!',
                                html: type == 'text' ? data.responseText : html,
                                icon: "error",
                            });
                        }
                    });
                }
            })
        }

        function hapus(id) {
            var previousWindowKeyDown = window.onkeydown;
            Swal.fire({
                title: 'Proses Aksi Ini?',
                text: "Proses ini tidak bisa dikembalikan!",
                allowEnterKey: true,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya lanjutkan!',
                cancelButtonText: 'Tidak!',
                showLoaderOnConfirm: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    window.onkeydown = previousWindowKeyDown;
                    overlay(true);
                    $.ajax({
                        url: '{{ route('destroy-faq') }}',
                        data: {
                            id: id,
                            _token: '{{ csrf_token() }}',
                        },
                        type: 'post',
                        dataType: 'json',
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: 'Success',
                                    text: data.message,
                                    icon: "success",
                                }).then(() => {
                                    // location.reload();
                                })
                            } else if (data.status == 2) {
                                Swal.fire({
                                    title: 'Oops Something Wrong',
                                    html: data.message,
                                    icon: "warning",
                                });
                            } else {
                                Swal.fire({
                                    title: 'Oops Something Wrong',
                                    html: data,
                                    icon: "warning",
                                });
                            }
                            overlay(false);
                            table.ajax.reload(null, false);
                        },
                        error: function(data) {
                            overlay(false);
                            var html = '';
                            if (data.responseJSON == undefined) {
                                html = data.responseText;
                            } else {
                                Object.keys(data.responseJSON).forEach(element => {
                                    html += data.responseJSON[element][0] + '<br>';
                                });
                            }

                            Swal.fire({
                                title: 'Oops Something Wrong!',
                                html: data.responseJSON == undefined ? data.responseText : data
                                    .responseJSON.message,
                                icon: "error",
                            });
                        }
                    });
                }
            })
        }
    </script>
@endsection
