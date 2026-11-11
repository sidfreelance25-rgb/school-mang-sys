@extends('layouts.app')
@section('title','Attendance Report')
@section('heading','Attendance Report')
@section('content')
<form method="GET" class="panel p-4 mb-4 flex gap-2"><input class="form-input w-40" type="date" name="from" value="{{ $from }}"><input class="form-input w-40" type="date" name="to" value="{{ $to }}"><button class="btn-soft">Filter</button><button type="button" class="btn-brand" onclick="window.print()">Print</button></form>
<div class="grid gap-4 lg:grid-cols-2">
<div class="panel print-area"><h3 class="p-4 font-semibold">Students</h3><div class="table-wrap"><table class="data"><thead><tr><th>Date</th><th>Name</th><th>Status</th></tr></thead><tbody>
@foreach($studentRecords as $row)<tr><td>{{ $row->date->format('d M Y') }}</td><td>{{ $row->student->user->name }}</td><td>{{ ucfirst($row->status) }}</td></tr>@endforeach
</tbody></table></div></div>
<div class="panel print-area"><h3 class="p-4 font-semibold">Teachers</h3><div class="table-wrap"><table class="data"><thead><tr><th>Date</th><th>Name</th><th>Status</th></tr></thead><tbody>
@foreach($teacherRecords as $row)<tr><td>{{ $row->date->format('d M Y') }}</td><td>{{ $row->teacher->user->name }}</td><td>{{ ucfirst($row->status) }}</td></tr>@endforeach
</tbody></table></div></div>
</div>
@endsection