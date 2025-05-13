@extends('layouts.layout')

@section('title', 'User List')

@section('content')
    <div>
        <h1>Stores</h1>
        <table>
            <tr>
                <th>Store Name</th>
                <th>Owner</th>
            </tr>
            @foreach ($stores as $store)
                <tr>
                    <td>{{ $store->store_name }}</td>
                    <td>{{ $store->user->name }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endsection
