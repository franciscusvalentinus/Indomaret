@extends('dashboard')
@section('content')

    <style>
        .table-striped tr:nth-child(odd) td,
        .table-striped tr:nth-child(odd) th {
            background-color: #dfe6e9;
        }
             /* Always set the map height explicitly to define the size of the div
              * element that contains the map. */
         #map {
             height: 100%;
         }
        /* Optional: Makes the sample page fill the window. */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
    <div class="container-fluid" style="margin-top: 100px;padding-left: 300px;padding-right: 200px;">
        <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <div class="panel-body progress-panel">
                        <div class="row">
                            <div class="col-lg-8 task-progress pull-left">
                                <h1>Peta Toko</h1>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div id="map"></div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <script type="text/javascript">

        var map;

        $( document ).ready(function() {
            //
        });

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: -34.397, lng: 150.644},
                zoom: 10
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=putkeyhere&callback=initMap"
            async defer></script>
@endsection