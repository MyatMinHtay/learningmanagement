<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Assignment Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .filters {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .badge-success { background-color: #28a745; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .text-muted { color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Assignment Reports</h1>
        <p>Generated on {{ date('F d, Y H:i:s') }}</p>
    </div>

    @if($request->has('teacher') || $request->has('course') || $request->has('status') || $request->has('date_from') || $request->has('date_to'))
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if($request->teacher)
            <p><strong>Teacher:</strong> {{ $teachers->find($request->teacher)->username ?? 'Unknown' }}</p>
        @endif
        @if($request->course)
            <p><strong>Course:</strong> {{ $request->course }}</p>
        @endif
        @if($request->status)
            <p><strong>Status:</strong> {{ ucfirst($request->status) }}</p>
        @endif
        @if($request->date_from)
            <p><strong>From Date:</strong> {{ $request->date_from }}</p>
        @endif
        @if($request->date_to)
            <p><strong>To Date:</strong> {{ $request->date_to }}</p>
        @endif
    </div>
    @endif

    <div>
        <p><strong>Total Assignments:</strong> {{ $assignments->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Assignment Title</th>
                <th>Course</th>
                <th>Created By</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Submissions</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assignments as $assignment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $assignment->assignment_title ?? 'Untitled Assignment' }}</td>
                    <td>
                        @if($assignment->course)
                            {{ $assignment->course->name }}
                            @if($assignment->course->creator)
                                <br><small>by {{ $assignment->course->creator->username }}</small>
                            @endif
                        @else
                            No Course
                        @endif
                    </td>
                    <td>
                        @if($assignment->course && $assignment->course->creator)
                            {{ $assignment->course->creator->username }}
                        @else
                            Unknown
                        @endif
                    </td>
                    <td>
                        @if($assignment->due_date)
                            {{ \Carbon\Carbon::parse($assignment->due_date)->format('M d, Y H:i') }}
                        @else
                            No due date
                        @endif
                    </td>
                    <td>
                        @if($assignment->status == 'active')
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $submissionCount = $assignment->submissions ? $assignment->submissions->count() : 0;
                            $enrolledCount = $assignment->course && $assignment->course->students ? $assignment->course->students->count() : 0;
                        @endphp
                        {{ $submissionCount }} / {{ $enrolledCount }}
                        @if($enrolledCount > 0)
                            <br><small>({{ number_format(($submissionCount / $enrolledCount) * 100, 1) }}%)</small>
                        @endif
                    </td>
                    <td>{{ $assignment->created_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No assignments found matching your criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>