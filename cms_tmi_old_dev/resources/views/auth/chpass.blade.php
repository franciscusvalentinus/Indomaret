@extends('app')
@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-2 col-md-offset-0">
            @if (session('newuser'))
                <div class="alert alert-warning">
                    <strong>Anda harus mengubah password terlebih dahulu sebelum dapat melakukan transaksi</strong> <br><br>
                </div>
            @endif
            @if (session('err'))
                <div class="alert alert-danger">
                    <strong>Password lama anda tidak sesuai dengan data kami.</strong> <br><br>
                </div>
            @endif
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="col-md-8 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="fa fa-key" style="font-size:larger"></i> &nbsp; Ganti Kata Sandi</div>

                <div class="panel-body">
                    <form action="updpassword" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class='input-group' style='margin-bottom: 5px;'>
                            <span class='input-group-addon' style="min-width:190px; text-align: left;">Kata sandi baru</span>
                            <input type='password' class='form-control' name='KataSandiBaru'/>
                        </div>
                        <div class='input-group' style='margin-bottom: 5px;'>
                            <span class='input-group-addon' style="min-width:190px; text-align: left;">Ulangi kata sandi baru</span>
                            <input type='password' class='form-control' name='KataSandiBaru_confirmation'/>
                        </div>
                        @if (session('suc'))
                            <div style="text-align: center" class="alert alert-success">
                                <strong>Password berhasil diubah.</strong>
                            </div>
                        @endif
                        <div style="text-align: center">
                            <button class="btn btn-success"><i class="fa fa-lock"></i> Ganti Kata Sandi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>