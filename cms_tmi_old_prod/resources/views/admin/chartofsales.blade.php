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
                <section class="panel">
                    <div class="panel-body">
                        <canvas id="myChart" width="400" height="800"></canvas>
                    </div>
                </section>
            </div>
        </div>
        <form id="chartform" method="get" action="laporansales">
{{--            <input type="text" name="_token" value="{{ csrf_token() }}" hidden>--}}
            <input type="text" id="branchid" name="branchid" hidden>
        </form>
    </div>
    <script>
        var ctx = document.getElementById('myChart').getContext('2d');
        $.ajax({
            url:'getchartdata',
            type : 'POST',
            data : {
                '_token' : '{{ csrf_token() }}'
            },
            dataType : 'json',
            success:function(response){

                var branch = [];
                var total = [];
                var branchid = [];

                for(var i in response) {
                    branch.push(response[i].branchname);
                    total.push(response[i].total);
                    branchid.push(response[i].id);
                }

                var myChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: branch,
                        datasets: [{
                            data: total,
                            borderWidth: 1,
                            backgroundColor: palette('qualitative', branch.length).map(function(hex) {
                                return '#' + hex;
                            })
                        }]
                    },
                    options: {
                        legend: {
                            position: 'right',
                            labels: {
                                fontSize: 14,
                                padding: 30
                            }
                        },
                        maintainAspectRatio: false,
                        onClick: function(c,i){
                            if(i[0])
                            {
                                var form = $('#chartform');
                                $('#branchid').val(branchid[i[0]._index]);
                                form.submit();
                            }
                        },
                        tooltips: {
                            mode: 'single',
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var value = data.datasets[0].data[tooltipItem.index];
                                    var branchname = data.labels[tooltipItem.index];
                                    if(parseInt(value) >= 1000){
                                        return [branchname,'Rp' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")];
                                    } else {
                                        return [branchname,'Rp' + value];
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });

        function graphClickEvent(event, array)
        {
        }

    </script>
@endsection