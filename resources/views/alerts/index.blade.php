@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Alerts</div>

            <table class="table">
                <colgroup>
                    <col width="200px">
                    <col>
                    <col width="100px">
                    <col width="100px">
                    <col width="100px">
                </colgroup>
                <thead>
                <tr>
                    <th>Time</th>
                    <th>Pair</th>
                    <th>Type</th>
                    <th>Volume</th>
                    <th>Order</th>
                </tr>
                </thead>
                <tbody>
                @foreach($alerts as $alert)
                    <tr>
                        <th>
                            <small>{{ $alert->created_at->format('d.F.Y H:i:s') }}</small>
                            <br/>
                            <span class="badge @if($alert->status == \App\Alert::STATUS_FAILED) badge-warning @else badge-info @endif">{{ $alert->status }}</span>
                        </th>
                        <td>
                            {{ $alert->pair }}
                        </td>
                        <td>
                            {{ $alert->type }}
                        </td>
                        <td>
                            {{ $alert->volume }}
                        </td>
                        <td>
                            @if($alert->order)
                            Order: {{ $alert->order->id }}
                            @endif
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
@endsection
