<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quiz Reports</title>
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
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Quiz Reports</h1>
        <p>Generated on {{ date('F d, Y H:i:s') }}</p>
    </div>

    @if($request->has('teacher') || $request->has('course') || $request->has('date_from') || $request->has('date_to'))
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if($request->teacher)
            <p><strong>Teacher:</strong> {{ $teachers->find($request->teacher)->username ?? 'Unknown' }}</p>
        @endif
        @if($request->course)
            <p><strong>Course:</strong> {{ $request->course }}</p>
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
        <p><strong>Total Quizzes:</strong> {{ $quizzes->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Quiz Title</th>
                <th>Course</th>
                <th>Created By</th>
                <th>Questions</th>
                <th>Attempts</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quizzes as $quiz)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $quiz->title ?? 'Untitled Quiz' }}</td>
                    <td>
                        @if($quiz->course)
                            {{ $quiz->course->name }}
                            @if($quiz->course->creator)
                                <br><small>by {{ $quiz->course->creator->username }}</small>
                            @endif
                        @else
                            No Course
                        @endif
                    </td>
                    <td>
                        @if($quiz->course && $quiz->course->creator)
                            {{ $quiz->course->creator->username }}
                        @else
                            Unknown
                        @endif
                    </td>
                    <td>
                        @php
                            $questionCount = $quiz->questions ? $quiz->questions->count() : 0;
                        @endphp
                        <span class="badge badge-info">{{ $questionCount }} questions</span>
                    </td>
                    <td>
                        @php
                            $attemptCount = $quiz->attempts ? $quiz->attempts->count() : 0;
                        @endphp
                        <span class="badge badge-secondary">{{ $attemptCount }} attempts</span>
                    </td>
                    <td>{{ $quiz->created_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No quizzes found matching your criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>