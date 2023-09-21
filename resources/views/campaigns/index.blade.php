@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-4 mt-5 mb-4">Campaigns</h1>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="row justify-content-end mb-3">
        <div class="col-md-2 text-right">
            <a href="{{ route('campaigns.create') }}" class="btn btn-success">Add Campaign</a>
        </div>
    </div>
    <table id="campaignsTable" class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Page Name</th>
                <th>Name</th>
                <th>Budget</th>
                <th>Target Audience</th>
                <th>Ad Content</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($campaigns as $campaign)
                <tr>
                    <td>{{ $campaign->campaign_id }}</td>
                    <td>{{ $campaign->page_name }}</td>
                    <td>{{ $campaign->name }}</td>
                    <td>${{ number_format($campaign->budget, 2) }}</td>
                    <td>{{ $campaign->target_audience }}</td>
                    <td>{{ $campaign->ad_content }}</td>
                    <td>{{ date('d-m-Y h:i a', strtotime($campaign->start_date)) }}</td>
                    <td>{{ date('d-m-Y h:i a', strtotime($campaign->end_date)) }}</td>
                    <td>
                        <a href="{{ route('campaigns.edit', $campaign->id) }}" class="btn btn-primary">Edit</a>
                    </td>
                    <td>
                        <form action="{{ route('campaigns.destroy', $campaign->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="background-color:#dc3545">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#campaignsTable').DataTable();
    });
</script>
@endsection
