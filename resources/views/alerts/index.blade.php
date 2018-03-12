@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Alerts</div>

                    <table class="table">
                        <colgroup>
                            <col width="200px">
                            <col>
                            <col width="100px">
                            <col width="100px">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>Time</th>
                            <th>Pair</th>
                            <th>Type</th>
                            <th>Volume</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($alerts as $alert)
                            <tr>
                                <th>
                                    <small>{{ date_formatted($alert->created_at) }}</small>
                                    <br/>
                                    <span class="badge @if($alert->isFailed()) badge-warning @elseif($alert->isProcessed()) badge-success  @else badge-info @endif">{{ $alert->status }}</span>
                                </th>
                                <td>
                                    {{ $alert->pair }}

                                    @if($alert->order)
                                        @include('order.information', ['order' => $alert->order])
                                    @endif
                                </td>
                                <td>
                                    {{ $alert->type }}
                                </td>
                                <td>
                                    {{ $alert->volume }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>

                    </table>

                    <div class="card-footer">
                        {!! $alerts->render() !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <balance></balance>
            </div>
        </div>
    </div>
@endsection
