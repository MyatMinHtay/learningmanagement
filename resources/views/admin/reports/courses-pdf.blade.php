<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Course Reports</title>
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
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }
        .badge-success { background-color: #28a745; color: white; }
        .text-muted { color: #6c757d; }
        .small { font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Course Reports</h1>
        <p>Generated on: {{ date('F d, Y H:i:s') }}</p>
    </div>

    @if($request->has('teacher') || $request->has('category') || $request->has('date_from') || $request->has('date_to'))
    <div class="filters">
        <h3>Applied Filters:</h3>
        @if($request->has('teacher') && $request->teacher != '')
            <p><strong>Teacher:</strong> {{ $teachers->find($request->teacher)->username ?? 'Unknown' }}</p>
        @endif
        @if($request->has('category') && $request->category != '')
            <p><strong>Category:</strong> {{ $categories->find($request->category)->name ?? 'Unknown' }}</p>
        @endif
        @if($request->has('date_from') && $request->date_from != '')
            <p><strong>From Date:</strong> {{ $request->date_from }}</p>
        @endif
        @if($request->has('date_to') && $request->date_to != '')
            <p><strong>To Date:</strong> {{ $request->date_to }}</p>
        @endif
    </div>
    @endif

    <p><strong>Total Courses:</strong> {{ $courses->count() }} courses found</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Course Name</th>
                <th>Category</th>
                <th>Created By</th>
                <th>Students Enrolled</th>
                <th>Duration</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($courses as $index => $course)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $course->name }}</strong>
                        @if($course->description)
                            <br>
                            <span class="small text-muted">{{ Str::limit(strip_tags($course->description), 50) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($course->category)
                            <span class="badge badge-info">{{ $course->category->name }}</span>
                        @else
                            <span class="badge badge-secondary">No Category</span>
                        @endif
                    </td>
                    <td>
                        @if($course->creator)
                            <strong>{{ $course->creator->username }}</strong>
                            <br><span class="small text-muted">{{ $course->creator->email }}</span>
                        @else
                            <span class="text-muted">Unknown</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-success">{{ $course->students->count() }} students</span>
                    </td>
                    <td>{{ $course->duration ?? 'Not specified' }}</td>
                    <td>{{ $course->created_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        No courses found matching your criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>