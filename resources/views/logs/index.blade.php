@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Logs</div>

            <table class="table">
                <colgroup>
                    <col width="200px">
                    <col>
                </colgroup>
                <thead>
                <tr>
                    <th>Time</th>
                    <th>Message</th>
                </tr>
                </thead>
                <tbody>
                @foreach($logs as $log)
                    <tr>
                        <th>
                            <small>{{ $log->created_at->format('d.m.Y H:i:s') }}</small>
                            <br />
                            <span class="badge badge-info">{{ $log->type }}</span>
                        </th>
                        <td>
                            <textarea class="form-control" disabled> {{ $log->message }}</textarea>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>

            <div class="card-footer">
                {!! $logs->render() !!}
            </div>
        </div>
    </div>
@endsection
