<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Course Details - {{ $course->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .course-info {
            margin-bottom: 30px;
        }
        .course-info h2 {
            color: #007bff;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            flex-shrink: 0;
        }
        .info-value {
            flex: 1;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 11px;
        }
        .students-section h2 {
            color: #007bff;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .no-students {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Course Details Report</h1>
        <p><strong>{{ $course->name }}</strong></p>
        <p>Generated on {{ date('F d, Y \\a\\t H:i') }}</p>
    </div>

    <!-- Course Information -->
    <div class="course-info">
        <h2>Course Information</h2>
        <div class="info-row">
            <div class="info-label">Course Name:</div>
            <div class="info-value">{{ $course->name }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Category:</div>
            <div class="info-value">
                @if($course->category)
                    {{ $course->category->name }}
                @else
                    No category
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Created By:</div>
            <div class="info-value">
                @if($course->creator)
                    {{ $course->creator->username }} ({{ $course->creator->email }})
                @else
                    Unknown
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Duration:</div>
            <div class="info-value">{{ $course->duration ? $course->duration . ' hours' : 'Not specified' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Created Date:</div>
            <div class="info-value">{{ $course->created_at->format('M d, Y H:i') }}</div>
        </div>
        @if($course->description)
        <div class="info-row">
            <div class="info-label">Description:</div>
            <div class="info-value">{{!! $course->description !!}}</div>
        </div>
        @endif
    </div>

    <!-- Course Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total_students'] }}</div>
            <div class="stat-label">Enrolled Students</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total_quizzes'] }}</div>
            <div class="stat-label">Quizzes</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total_assignments'] }}</div>
            <div class="stat-label">Assignments</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total_modules'] }}</div>
            <div class="stat-label">Modules</div>
        </div>
    </div>

    <!-- Enrolled Students -->
    <div class="students-section">
        <h2>Enrolled Students ({{ $stats['total_students'] }})</h2>
        
        @if($course->students->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 25%;">Student Name</th>
                        <th style="width: 30%;">Email</th>
                        <th style="width: 20%;">Position</th>
                        <th style="width: 20%;">Enrollment Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($course->students as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->username }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->position ?? 'Not specified' }}</td>
                            <td>{{ $student->pivot->created_at ? $student->pivot->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-students">
                <p>No students are currently enrolled in this course.</p>
            </div>
        @endif
    </div>

    <!-- Course Quizzes -->
    <div class="students-section">
        <h2>Course Quizzes ({{ $stats['total_quizzes'] }})</h2>
        
        @if($course->coursequizzes && $course->coursequizzes->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 35%;">Quiz Title</th>
                        <th style="width: 20%;">Questions</th>
                        <th style="width: 20%;">Duration</th>
                        <th style="width: 20%;">Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($course->coursequizzes as $index => $quiz)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $quiz->title }}</td>
                            <td>{{ $quiz->questions->count() ?? 0 }} questions</td>
                            <td>{{ $quiz->is_time_limited ? $quiz->total_time . ' minutes' : 'No limit' }}</td>
                            <td>{{ $quiz->created_at ? $quiz->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-students">
                <p>No quizzes are available for this course.</p>
            </div>
        @endif
    </div>

    <!-- Course Assignments -->
    <div class="students-section">
        <h2>Course Assignments ({{ $stats['total_assignments'] }})</h2>
        
        @if($course->assignments && $course->assignments->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 30%;">Assignment Title</th>
                        <th style="width: 20%;">Student</th>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 15%;">Mark</th>
                        <th style="width: 15%;">Submitted Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($course->assignments as $index => $assignment)
                    
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $assignment->assignment_title ?? 'Unknown' }}</td>
                            <td>
                                @if($assignment->student)
                                    {{ $assignment->student->username }}
                                @else
                                    Unknown
                                @endif
                            </td>
                            <td>
                                @if($assignment->status == 'accepted')
                                    Accepted
                                @elseif($assignment->status == 'rejected')
                                    Rejected
                                @else
                                    Pending
                                @endif
                            </td>
                            <td>{{ $assignment->mark ?? 'Not graded' }}</td>
                            <td>{{ $assignment->created_at ? $assignment->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        @else
            <div class="no-students">
                <p>No assignments are available for this course.</p>
            </div>
        @endif
    </div>

    <!-- Course Modules -->
    <div class="students-section">
        <h2>Course Modules ({{ $stats['total_modules'] }})</h2>
        
        @if($course->modules && $course->modules->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Module Title</th>
                        <th>Module Content</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($course->modules as $index => $module)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $module->title }}</td>
                            <td>{{ Str::limit($module->content, 50, '...') }}</td>
                            <td>{{ $module->created_at ? $module->created_at->format('M d, Y') : 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="no-students">
                <p>No modules are available for this course.</p>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Learning Management System - Course Details Report</p>
        <p>This report was generated automatically on {{ date('F d, Y \\a\\t H:i') }}</p>
    </div>
</body>
</html>