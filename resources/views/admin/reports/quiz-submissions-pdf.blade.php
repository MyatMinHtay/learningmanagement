<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quiz Submission Reports</title>
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
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Quiz Submission Reports</h1>
        <p>Generated on {{ date('F d, Y H:i:s') }}</p>
    </div>

    @if($request->has('student') || $request->has('quiz') || $request->has('course') || $request->has('grade_min') || $request->has('grade_max') || $request->has('date_from') || $request->has('date_to'))
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if($request->student)
            <p><strong>Student:</strong> {{ $students->find($request->student)->username ?? 'Unknown' }}</p>
        @endif
        @if($request->quiz)
            <p><strong>Quiz:</strong> {{ $quizzes->find($request->quiz)->title ?? 'Unknown' }}</p>
        @endif
        @if($request->course)
            <p><strong>Course:</strong> {{ $request->course }}</p>
        @endif
        @if($request->grade_min)
            <p><strong>Min Grade:</strong> {{ $request->grade_min }}%</p>
        @endif
        @if($request->grade_max)
            <p><strong>Max Grade:</strong> {{ $request->grade_max }}%</p>
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
                <th>Quiz</th>
                <th>Course</th>
                <th>Score</th>
                <th>Grade</th>
                <th>Attempt Date</th>
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
                        @if($submission->quiz)
                            {{ $submission->quiz->title }}
                        @else
                            Unknown Quiz
                        @endif
                    </td>
                    <td>
                        @if($submission->quiz && $submission->quiz->course)
                            {{ $submission->quiz->course->name }}
                        @else
                            No Course
                        @endif
                    </td>
                    <td>{{ $submission->score ?? 0 }}%</td>
                    <td>
                        @php
                            $score = $submission->score ?? 0;
                            $gradeClass = 'badge-danger';
                            $grade = 'F';
                            if ($score >= 90) {
                                $gradeClass = 'badge-success';
                                $grade = 'A';
                            } elseif ($score >= 80) {
                                $gradeClass = 'badge-info';
                                $grade = 'B';
                            } elseif ($score >= 70) {
                                $gradeClass = 'badge-warning';
                                $grade = 'C';
                            } elseif ($score >= 60) {
                                $gradeClass = 'badge-warning';
                                $grade = 'D';
                            }
                        @endphp
                        <span class="badge {{ $gradeClass }}">{{ $grade }}</span>
                    </td>
                    <td>{{ $submission->created_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">No submissions found matching your criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>