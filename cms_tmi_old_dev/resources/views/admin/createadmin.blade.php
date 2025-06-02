@extends('dashboard')

@section('content')
    <div class="container-fluid" style="margin-top: 100px;padding-left: 200px;padding-right: 30px;">
        <div class="row">
            <div class="col-md-12">

                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>Whoops!</strong> There were some problems with your input.<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="panel panel-default col-md-7 col-md-offset-2">
                    <div class="panel-heading"><i style='font-size: larger;' class='fa fa-user-plus'></i> Tambah Admin Baru</div>
                    <div class="panel-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ url('/addadmin') }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group">
                                <label class="col-md-4 control-label">Nama <b style="color:red">*</b></label>
                                <div class="col-md-6">
                                    <input type="text" class="form-control" maxlength="50" name="name" value="{{ old('name') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Alamat Email <b style="color:red">*</b></label>
                                <div class="col-md-6">
                                    <input type="email" class="form-control" maxlength="50" name="email" value="{{ old('email') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Password <b style="color:red">*</b></label>
                                <div class="col-md-6">
                                    <input type="password" class="form-control" maxlength="50" name="password">
                                    <span style="text-align: center; color: red">[Password harus terdiri dari minimal 6 karakter dengan campuran huruf besar, huruf kecil, angka dan simbol]</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Confirm Password <b style="color:red">*</b></label>
                                <div class="col-md-6">
                                    <input type="password" class="form-control" maxlength="50" name="password_confirmation">
                                </div>
                            </div>

                            {{--<div class="form-group" style="margin-top: 10px;">--}}
                                {{--<label class="col-md-4 control-label">Pilih Role :</label>--}}
                                {{--<div class="col-sm-6">--}}
                                    {{--<select id='rol' style="padding-left: 20px;" class="selectpicker" name="rolestatus">--}}
                                        {{--<option value="x">--Pilih Status--</option>--}}
                                        {{--<option value="2">Admin Cabang</option>--}}
                                        {{--<option value="3">Admin HO</option>--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}

                            <div class="form-group" style="margin-top: 20px;">
                                <label class="col-md-4 control-label">Pilih Cabang <b style="color:red">*</b></label>
                                <select name="cabang" id="cabang" class="col-md-6 selectpicker" data-live-search="true" onchange="changeCabang()">
                                    {!! $branch !!}
                                </select>
                            </div>

                            <div style="text-align: center; font-size:larger; margin-bottom: 10px">
                                <i>(<b style="color:red">*</b>) Harus Diisi </i>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-success">
                                        <i style='color:white' class='fa fa-user-plus'></i>
                                        Simpan User
                                    </button>
                                    <a class='btn btn-primary' href="adminlist"><i style='color:white' class='fa fa-user'></i> Kembali ke List Member</a>
                                </div>
                            </div>

                            {{--<div data-theme="light" id="rajaongkir-widget"></div>--}}
                            {{--<script type="text/javascript" src="//rajaongkir.com/script/widget.js"></script>--}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showCabang() {
            var e = $('#rol').val();
            if(e == 2){
                $('#cabang').show();
            }else if(e == 3){
                $('#cabang').hide();
            }
        }
    </script>
@endsection
