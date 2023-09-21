@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-5">
                <div class="card-header">Edit Campaign</div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('campaigns.update', $campaign->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Page ID Dropdown -->
                        <div class="form-group">
                            <label for="page_id">Page:</label>
                            <select name="page_id" id="page_id" class="form-control">
                                <option value="0">--- Select Facebook Page ---</option>
                                @foreach ($pages as $page)
                                    <option value="{{ $page->page_id }}" {{ old('page_id', $campaign->page_id) == $page->page_id ? 'selected' : '' }}>{{ $page->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Campaign Name -->
                        <div class="form-group">
                            <label for="name">Campaign Name:</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $campaign->name) }}" required>
                        </div>

                        <!-- Budget -->
                        <div class="form-group">
                            <label for="budget">Budget:</label>
                            <input type="number" name="budget" id="budget" class="form-control" step="0.01" value="{{ old('budget', $campaign->budget) }}" required>
                        </div>

                        <!-- Target Audience -->
                        <div class="form-group">
                            <label for="target_audience">Target Audience:</label>
                            <input type="text" name="target_audience" id="target_audience" class="form-control" value="{{ old('target_audience', $campaign->target_audience) }}" required>
                        </div>

                        <!-- Ad Content -->
                        <div class="form-group">
                            <label for="ad_content">Ad Content:</label>
                            <textarea name="ad_content" id="ad_content" class="form-control" rows="4" required>{{ old('ad_content', $campaign->ad_content) }}</textarea>
                        </div>

                        <!-- Start Date -->
                        <div class="form-group">
                            <label for="start_date">Start Date:</label>
                            <input type="datetime-local" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $campaign->start_date) }}" required>
                        </div>

                        <!-- End Date -->
                        <div class="form-group">
                            <label for="end_date">End Date:</label>
                            <input type="datetime-local" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $campaign->end_date) }}" required>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" style="background-color:#007bff">Update Campaign</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
