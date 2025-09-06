<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Assignment Submission Reports</title>
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
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
        }
        .badge-success { background-color: #28a745; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Assignment Submission Reports</h1>
        <p>Generated on {{ date('F d, Y H:i:s') }}</p>
    </div>

    @if($request->has('student') || $request->has('course') || $request->has('teacher') || $request->has('status') || $request->has('date_from') || $request->has('date_to'))
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if($request->student)
            <p><strong>Student:</strong> {{ $students->find($request->student)->username ?? 'Unknown' }}</p>
        @endif
        @if($request->course)
            <p><strong>Course:</strong> {{ $request->course }}</p>
        @endif
        @if($request->teacher)
            <p><strong>Teacher:</strong> {{ $teachers->find($request->teacher)->username ?? 'Unknown' }}</p>
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
        <p><strong>Total Submissions:</strong> {{ $submissions->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student</th>
                <th>Assignment</th>
                <th>Course</th>
                <th>Teacher</th>
                <th>Status</th>
                <th>Mark</th>
                <th>Submitted Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($submissions as $submission)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($submission->user)
                            {{ $submission->user->username }}
                        @else
                            Unknown Student
                        @endif
                    </td>
                    <td>
                        @if($submission->assignment_title)
                            {{ $submission->assignment_title }}
                        @else
                            Untitled Assignment
                        @endif
                    </td>
                    <td>
                        @if($submission->course)
                            {{ $submission->course->name }}
                        @else
                            No Course
                        @endif
                    </td>
                    <td>
                        @if($submission->course && $submission->course->creator)
                            {{ $submission->course->creator->username }}
                        @else
                            Unknown
                        @endif
                    </td>
                    <td>
                        @if($submission->mark !== 0)
                            <span class="badge badge-success">Accepted</span>
                        @elseif($submission->files && $submission->files != '[]')
                            <span class="badge badge-info">Pending</span>
                        @else
                            <span class="badge badge-secondary">Not Submitted</span>
                        @endif
                    </td>
                    <td>
                        @if($submission->mark !== 0)
                            {{ $submission->mark }}
                        @else
                            Not Graded
                        @endif
                    </td>
                    <td>
                        @if($submission->updated_at)
                            {{ \Carbon\Carbon::parse($submission->updated_at)->format('M d, Y H:i') }}
                        @else
                            Not submitted
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center;">No submissions found matching your criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>