@extends('dashboard')
@section('content')
    <div class="container-fluid" style="margin-top: 100px;padding-left: 300px;padding-right: 300px;">
        <div class="col-md-12">
            <div class="panel panel-default flat">
                <div class="panel-body project-team">
                    <div class="task-progress">
                        <h1>Upload File PLU</h1>
                    </div>
                </div>
                @if(Session::has('error_message'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Warning!</strong> {!!  Session::get('error_message')  !!}
                    </div>
                @elseif (Session::has('success_message'))
                    <div class="alert alert-info alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <strong>Success!</strong> {!!  Session::get('success_message')  !!}
                    </div>
                @endif

                @if (isset($nodata))
                    <div class="alert alert-block alert-danger fade in">
                        <button data-dismiss="alert" class="close close-sm" type="button">
                            <i class="icon-remove"></i>
                        </button>
                        <strong>Info !</strong> {{$nodata}}
                    </div>
                @endif

                <div class="panel-body">
                    <form class="form-horizontal" role="form" enctype="multipart/form-data" method="POST" action="{{ url('uploadplu') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Pilih Cabang IGR <b style="color:red">*</b></label>
                            <div class="col-md-6">
                                <select class="js-example-basic-single" id='txt_tipe' name="cabang">
                                    {!! $branch !!}
                                </select>
                            </div>
                        </div>

                        <div style="margin-bottom: 10px;"><span style="font-size: large !important;">Import File From</span></div>
                        <div>
                            <input id="inputv" name="import_file" type="file" multiple class="file-loading igr-flat">
                        </div>
                        {{--<div class="progress">--}}
                        {{--<div class="bar"></div >--}}
                        {{--<div class="percent">0%</div >--}}
                        {{--</div>--}}
                    </form>
                    <div class="form-group" style="margin-top: 10px;">
                            <span>
                            Gunakan Fitur ini untuk melakukan proses upload master PLU<br />
                            <a style="text-decoration: underline" href="{{ ('contents/contoh_form_order.csv') }}">Download Contoh Order Form File CSV</a>
                            </span><br><br>
                        Catatan: tipe file .csv

                    </div>
                    {{--@if (session('suc'))--}}
                    {{--<div class="col-md-12 igr-flat" style="margin-top:10px;">--}}
                    {{--<div class="alert alert-danger igr-flat">--}}
                    {{--<strong>{{session('suc')}}</strong>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--@endif--}}

                    {{--@if (isset($err))--}}
                    {{--<div class="col-md-12 igr-flat" style="margin-top:10px;">--}}
                    {{--<div class="alert alert-danger igr-flat">--}}
                    {{--<strong>{{$err}}</strong>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    {{--@endif--}}
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">

    </script>
@endsection
