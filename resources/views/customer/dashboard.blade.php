@extends('layouts.customer')

@section('content')
    <h1 class="text-2xl font-bold">
        Customer Dashboard
    </h1>

    <p>Danh sách hợp đồng:</p>

    @foreach($contracts as $contract)
        <div>
            Hợp đồng #{{ $contract->id }}
        </div>
    @endforeach
@endsection
