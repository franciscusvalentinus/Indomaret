@extends('dashboard')
@section('content')

    <style>
        .table-striped tr:nth-child(odd) td,
        .table-striped tr:nth-child(odd) th {
    background-color: #dfe6e9;
        }
    </style>
    <div class="container-fluid" style="margin-top: 100px;padding-left: 300px;padding-right: 200px;">
        <div class="row">
            <div class="col-md-12">
                <section class="panel" id="sectioninsert">
                    <div class="panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-8 task-progress pull-left">
                                <h1>Tambah Versi Baru</h1>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="errorarea" style="background-color: #d1282d; color: white; display:none;">
                                Auth Error
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">Versi</label>
                                <input type="text" class="form-control" id="insertver" name="insertver" value="" />
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Deskripsi</label>
                                <input type="text" class="form-control" id="insertdesc" name="insertdesc" value="" />
                            </div>
                        </div>
                        <div class="row" style="padding-top: 20px;">
                            <div class="col-md-12" style="text-align: right;">
                                <button type="button" class="btn btn-primary" onclick="submitInsert()">Submit</button>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="panel" id="sectionupdate" style="display:none;">
                    <div class="panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-8 task-progress pull-left">
                                <h1>Update Versi</h1>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="control-label">Versi</label>
                                <input type="text" id="updateid" name="updateid"  hidden/>
                                <input type="text" class="form-control" id="updatever" name="updatever" value="" />
                            </div>
                            <div class="col-md-6">
                                <label class="control-label">Deskripsi</label>
                                <input type="text" class="form-control" id="updatedesc" name="updatedesc" value="" />
                            </div>
                        </div>
                        <div class="row" style="padding-top: 20px;">
                            <div class="col-md-12" style="text-align: right;">
                                <button type="button" class="btn btn-primary" onclick="submitUpdate()">Submit</button>
                                <button type="button" class="btn btn-danger" onclick="cancelUpdate()">Cancel</button>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="panel">
                    <div class="panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-8 task-progress pull-left">
                                <h1>Daftar Versi</h1>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="datatable table table-striped table-bordered responsive" id="dtTable">
                            <thead>
                            <tr>
                                <th class="font-14" style="text-align: center;">Versi</th>
                                <th class="font-14" style="text-align: center;">Deskripsi</th>
                                <th class="font-14" style="text-align: center;">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <script>
        var token;
        var reqAccess = $.ajax({
            url:'devgetaccess',
            type : 'POST',
            data : {
                '_token' : '{{ csrf_token() }}'
            },
            dataType : 'json',
            success:function(response)
            {
                if(response.status === 0)
                {
                    alert(response.message);
                    $('#errorarea').css('display','');
                }
                else if(response.status === 1)
                {
                    token = response.access_token;
                }
            },
            fail: function(xhr, textStatus, errorThrown){
                $('#errorarea').css('display','');
            }
        });

        $( document ).ready(function() {
            reqAccess.done( getAllVersion() );
        });

        function updateClicked(id,ver,desc)
        {
            $('#updateid').val(id);
            $('#updatever').val(ver);
            $('#updatedesc').val(desc);
            $('#sectioninsert').css('display','none');
            $('#sectionupdate').css('display','');
        }

        function cancelUpdate()
        {
            $('#sectioninsert').css('display','');
            $('#sectionupdate').css('display','none');
        }

        function submitUpdate()
        {
            $.ajax({
                url:'devupdatever',
                type : 'POST',
                data : {
                    '_token' : '{{ csrf_token() }}',
                    'auth' : token,
                    'id' : $('#updateid').val(),
                    'ver' : $('#updatever').val(),
                    'desc' : $('#updatedesc').val()
                },
                dataType : 'json',
                success:function(response)
                {
                    if(response.status === -1)
                    {
                        reqAccess.done( submitUpdate() );
                    }
                    else if(response.status === 0)
                    {
                        if(response.message.server_id)
                        {
                            alert(response.message.server_id);
                        }
                        if(response.message.version_name)
                        {
                            alert(response.message.version_name);
                        }
                        if(response.message.description)
                        {
                            alert(response.message.description);
                        }
                    }
                    else if(response.status === 1)
                    {
                        alert(response.message);
                        getAllVersion();
                    }
                }
            });
        }

        function submitInsert()
        {
            $.ajax({
                url:'devinsertver',
                type : 'POST',
                data : {
                    '_token' : '{{ csrf_token() }}',
                    'auth' : token,
                    'ver' : $('#insertver').val(),
                    'desc' : $('#insertdesc').val()
                },
                dataType : 'json',
                success:function(response)
                {
                    if(response.status === -1)
                    {
                        reqAccess.done( submitInsert() );
                    }
                    else if(response.status === 0)
                    {
                        if(response.message.version_name)
                        {
                            alert(response.message.version_name);
                        }
                        if(response.message.description)
                        {
                            alert(response.message.description);
                        }
                    }
                    else if(response.status === 1)
                    {
                        alert(response.message);
                        getAllVersion();
                    }
                }
            });
        }

        function getAllVersion()
        {
            $.ajax({
                url:'devgetallver',
                type : 'POST',
                data : {
                    '_token' : '{{ csrf_token() }}',
                    'auth' : token
                },
                dataType : 'json',
                success:function(response)
                {
                    if(response.status === -1)
                    {
                        reqAccess.done( getAllVersion() );
                    }
                    else
                    {
                        var projTable = $('#dtTable').DataTable( {
                            dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                                "<'row'<'col-sm-12'tr>>" +
                                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                            ordering: true,
                            searching : true,
                            autoWidth: false,
                            data: response,
                            columns: [
                                { data: 'version_name', name: 'ver'},
                                { data: 'description', name: 'desc'},
                                { data: null, name: 'id', render: function ( data, type, row ) {
                                        return "<button class='btn' onclick='updateClicked(\""+row.server_id+"\",\""+row.version_name+"\",\""+row.description+"\")'>Update</button>";
                                    }
                                }
                            ],
                            columnDefs: [
                                { targets: [0, 2], 'width':'1%'}
                            ],
                            order: [0,'asc'],
                            bResetDisplay: true,
                            "bStateSave": true,
                            "bDestroy": true,
                            fixedColumns : {
                                leftColumns: 1
                            }
                        } );
                        projTable.draw();
                    }
                }
            });
        }
    </script>
@endsection