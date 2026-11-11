@extends('layouts.app')
@section('title','Exam Report')
@section('heading','Examination Performance')
@section('content')
<button class="btn-brand mb-4 no-print" onclick="window.print()">Print</button>
<div class="panel print-area"><div class="table-wrap"><table class="data"><thead><tr><th>Student</th><th>Exam</th><th>Subject</th><th>Marks</th><th>Grade</th></tr></thead><tbody>
@foreach($results as $result)
<tr>
<td>{{ $result->student->user->name }}</td>
<td>{{ $result->examSchedule->examination->name }}</td>
<td>{{ $result->examSchedule->subject->name }}</td>
<td>{{ $result->is_absent ? 'Absent' : $result->marks_obtained.'/'.$result->examSchedule->max_marks }}</td>
<td>{{ $result->grade }}</td>
</tr>
@endforeach
</tbody></table></div></div>
@endsection