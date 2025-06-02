@extends('dashboard')
@section('content')
    <div class="container-fluid" style="margin-top: 100px;padding-left: 300px;padding-right: 300px;">

        <div class="row">

            {{--<div class="col-sm-6 col-md-4 col-md-offset-2">--}}
                {{--<div class="thumbnail" style="padding-left: 20px;padding-top: 20px;padding-right: 20px;padding-bottom: 20px;">--}}
                    {{--<img src="{{ url('../resources/assets/img/aktif.png') }}" alt="...">--}}
                    {{--<div class="caption">--}}
                        {{--<h3>Ada <span class="label label-danger">{{ $aktif }}</span> </h3>--}}
                        {{--<p>Toko Status Aktif</p>--}}
                        {{--<p>--}}
                            {{--<a href="#" class="btn btn-primary" role="button">Button</a> --}}
                        {{--<a href="{{ url('/inputmember') }}" class="btn btn-default flat" role="button">Lihat</a>--}}
                        {{--</p>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

            {{--<div class="col-sm-6 col-md-4 col-md-offset-0">--}}
                {{--<div class="thumbnail" style="padding-left: 20px;padding-top: 20px;padding-right: 20px;padding-bottom: 20px;">--}}
                    {{--<img src="{{ url('../resources/assets/img/belumaktif.png') }}" alt="...">--}}
                    {{--<div class="caption">--}}
                        {{--<h3>Ada <span class="label label-danger">{{ $belumaktif }}</span></h3>--}}
                        {{--<p>Toko Status Belum Aktif</p>--}}
                        {{--<p>--}}
                            {{--<a href="#" class="btn btn-primary" role="button">Button</a> --}}
                        {{--<a href="{{ url('/inputmember') }}" class="btn btn-default flat" role="button">Lihat</a></p>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
        </div>

        {{--<section class="panel col-lg-12">--}}
                {{--<div class="panel-body project-team">--}}
                    {{--<div class="task-progress">--}}
                        {{--<h1>List PLU TMI</h1>--}}
                    {{--</div>--}}
                {{--</div>--}}
                    {{--<table class="table table-hover personal-task">--}}
                {{--<tbody>--}}

                {{--@foreach($listtoko as $index => $row)--}}
                {{--<tr>--}}
                {{--<td>--}}
                    {{--<p class="profile-name">{{ ucwords(strtolower($row->nama)) }}</p>--}}
                    {{--<p class="profile-occupation">{{ ucwords(strtolower($row->idtoko)) }}</p>--}}
                {{--</td>--}}
                {{--<td>--}}
                    {{--<h3><span class="label label-danger"> {{ $row->sumplu }}</span>&nbsp;item plu</h3>--}}
                    {{--<span class="badge bg-important">{{ $row->sumplu }} item</span>--}}
                {{--</td>--}}
                {{--<td style='font-size: large'>--}}
                    {{--<div class="btn-row">--}}
                        {{--<div class="btn-group">--}}
                            {{--<label class="btn btn-primary">--}}
                                {{--Lihat--}}
                            {{--</label>--}}
                            {{--<label class="btn btn-danger">--}}
                                {{--Manage--}}
                            {{--</label>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</td>--}}
                {{--</tr>--}}
                {{--@endforeach--}}
                {{--</tbody>--}}
                {{--</table>--}}
        {{--</section>--}}
                {{--<div class="page-404">--}}
                    {{--<p class="text-404">Hello!</p>--}}
        {{----}}
                    {{--<h2>Aww !</h2>--}}
                    {{--<p>Website ini sedang Maintenance. <br><a href="index.html">Return Home</a></p>--}}
                {{--</div>--}}

        </div>
    <script>
    </script>
@endsection
