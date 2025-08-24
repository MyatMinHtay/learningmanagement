<x-adminlayout>
    <section class="home container analytics-dashboard">
        <div class="text my-4">
            <h2><i class="fas fa-chart-line"></i> Learning Analytics Dashboard</h2>
            <p class="text-muted">Comprehensive insights into your learning management system</p>
        </div>

        <!-- Key Metrics Cards -->
        <div class="row mb-4">
            <div class="col-md-2 col-sm-6 mb-3">
                <div class="card metric-card">
                    <div class="card-body text-center">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <h4>{{ $totalUsers }}</h4>
                        <p class="mb-0">Total Users</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 mb-3">
                <div class="card metric-card bg-success">
                    <div class="card-body text-center">
                        <i class="fas fa-book fa-2x mb-2"></i>
                        <h4>{{ $totalCourses }}</h4>
                        <p class="mb-0">Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 mb-3">
                <div class="card metric-card bg-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-question-circle fa-2x mb-2"></i>
                        <h4>{{ $totalQuizzes }}</h4>
                        <p class="mb-0">Quizzes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 mb-3">
                <div class="card metric-card bg-info">
                    <div class="card-body text-center">
                        <i class="fas fa-tasks fa-2x mb-2"></i>
                        <h4>{{ $totalAssignments }}</h4>
                        <p class="mb-0">Assignments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 mb-3">
                <div class="card metric-card bg-secondary">
                    <div class="card-body text-center">
                        <i class="fas fa-play-circle fa-2x mb-2"></i>
                        <h4>{{ $totalLessons }}</h4>
                        <p class="mb-0">Lessons</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 mb-3">
                <div class="card metric-card bg-dark">
                    <div class="card-body text-center">
                        <i class="fas fa-user-graduate fa-2x mb-2"></i>
                        <h4>{{ $totalEnrollments }}</h4>
                        <p class="mb-0">Enrollments</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- User Distribution Chart -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="analytics-card">
                    <div class="analytics-card-header">
                        <h5><i class="fas fa-chart-pie"></i> User Distribution</h5>
                    </div>
                    <div class="analytics-card-body">
                        <div class="chart-container">
                            <canvas id="userDistributionChart" class="chart-canvas"></canvas>
                        </div>
                        <div class="mt-3 text-center">
                            <span class="custom-badge badge-primary-custom me-2">Admins: {{ $adminCount }}</span>
                            <span class="custom-badge badge-success-custom me-2">Teachers: {{ $teacherCount }}</span>
                            <span class="custom-badge badge-warning-custom">Students: {{ $studentCount }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Registrations Chart -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="analytics-card">
                    <div class="analytics-card-header">
                        <h5><i class="fas fa-chart-line"></i> Monthly Registrations</h5>
                    </div>
                    <div class="analytics-card-body">
                        <div class="chart-container">
                            <canvas id="monthlyRegistrationsChart" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Creation Chart -->
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="analytics-card">
                    <div class="analytics-card-header">
                        <h5><i class="fas fa-chart-bar"></i> Monthly Course Creation</h5>
                    </div>
                    <div class="analytics-card-body">
                        <div class="chart-container">
                            <canvas id="monthlyCoursesChart" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card performance-card">
                    <div class="card-body text-center">
                        <h3>{{ $quizPerformance->avg_score ?? 0 }}%</h3>
                        <p class="mb-0">Average Quiz Score</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card performance-card bg-gradient-success">
                    <div class="card-body text-center">
                        <h3>{{ $passRate }}%</h3>
                        <p class="mb-0">Quiz Pass Rate</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card performance-card bg-gradient-warning">
                    <div class="card-body text-center">
                        <h3>{{ $avgEnrollmentPerCourse }}</h3>
                        <p class="mb-0">Avg Enrollment/Course</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card performance-card bg-gradient-info">
                    <div class="card-body text-center">
                        <h3>{{ $assignmentSubmissionRate }}%</h3>
                        <p class="mb-0">Assignment Submission Rate</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performing Courses Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="analytics-table">
                    <div class="analytics-card-header">
                        <h5><i class="fas fa-trophy"></i> Top Performing Courses</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Course Name</th>
                                    <th>Created By</th>
                                    <th>Enrollments</th>
                                    <th>Completion Rate</th>
                                    <th>Avg Quiz Score</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCourses as $index => $course)
                                <tr>
                                    <td><strong>{{ $index + 1 }}</strong></td>
                                    <td>
                                        <strong>{{ $course->name }}</strong>
                                        <br><small class="text-muted">{{ Str::limit($course->description, 50) }}</small>
                                    </td>
                                    <td>{{ $course->creator->username ?? 'Unknown' }}</td>
                                    <td><span class="custom-badge badge-primary-custom">{{ $course->students_count }}</span></td>
                                    <td>
                                        <div class="custom-progress">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $course->completion_rate }}%"
                                                 aria-valuenow="{{ $course->completion_rate }}" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $course->completion_rate }}%</small>
                                    </td>
                                    <td><span class="custom-badge badge-warning-custom">{{ $course->avg_quiz_score }}%</span></td>
                                    <td><span class="custom-badge badge-success-custom">Active</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No course data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Tables -->
        <div class="row mb-4">
            <!-- Recent Quiz Submissions -->
            <div class="col-lg-6 mb-4">
                <div class="analytics-table">
                    <div class="analytics-card-header">
                        <h5><i class="fas fa-clock"></i> Recent Quiz Submissions</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Quiz</th>
                                    <th>Score</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentQuizSubmissions as $submission)
                                <tr>
                                    <td>{{ $submission->user->username ?? 'Unknown' }}</td>
                                    <td>{{ Str::limit($submission->quiz->title ?? 'Quiz', 20) }}</td>
                                    <td>
                                        <span class="custom-badge {{ $submission->score >= 70 ? 'badge-success-custom' : 'badge-danger-custom' }}">
                                            {{ $submission->score }}%
                                        </span>
                                    </td>
                                    <td><small>{{ $submission->created_at->format('M d, Y') }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No recent submissions</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Assignment Submissions -->
            <div class="col-lg-6 mb-4">
                <div class="analytics-table">
                    <div class="analytics-card-header">
                        <h5><i class="fas fa-file-alt"></i> Recent Assignment Submissions</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Assignment</th>
                                    <th>Course</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAssignmentSubmissions as $submission)
                                <tr>
                                    <td>{{ $submission->user->username ?? 'Unknown' }}</td>
                                    <td>{{ Str::limit($submission->assignment_title, 20) }}</td>
                                    <td>{{ Str::limit($submission->course->name ?? 'Course', 15) }}</td>
                                    <td><small>{{ $submission->created_at->format('M d, Y') }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No recent submissions</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submission Analytics Charts -->
        <div class="row mb-4">
            <!-- Quiz Submissions Trend Chart -->
            <div class="col-lg-6 mb-4">
                <div class="analytics-card">
                    <div class="analytics-card-header">
                        <h5><i class="fas fa-chart-line"></i> Quiz Submissions (31 Days)</h5>
                    </div>
                    <div class="analytics-card-body">
                        <div class="chart-container">
                            <canvas id="quizSubmissionsChart" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Submissions Trend Chart -->
            <div class="col-lg-6 mb-4">
                <div class="analytics-card">
                    <div class="analytics-card-header">
                        <h5><i class="fas fa-chart-bar"></i> Assignment Submissions (31 Days)</h5>
                    </div>
                    <div class="analytics-card-body">
                        <div class="chart-container">
                            <canvas id="assignmentSubmissionsChart" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quiz Score Distribution Chart -->
            {{-- <div class="col-lg-4 mb-4">
                <div class="analytics-card">
                    <div class="analytics-card-header">
                        <h5><i class="fas fa-chart-pie"></i> Quiz Score Distribution</h5>
                    </div>
                    <div class="analytics-card-body">
                        <div class="chart-container">
                            <canvas id="quizScoreDistributionChart" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>

        <!-- Course Enrollment Analytics -->
        <div class="row mb-4">
            <!-- Course Enrollment Distribution Chart -->
            <div class="col-lg-6 mb-4">
                <div class="analytics-card">
                    <div class="analytics-card-header">
                        <h5><i class="fas fa-chart-pie"></i> Course Enrollment Distribution</h5>
                    </div>
                    <div class="analytics-card-body">
                        <div class="chart-container">
                            <canvas id="courseEnrollmentChart" class="chart-canvas"></canvas>
                        </div>
                        <div class="mt-3">
                            <div class="row">
                                @foreach($courseEnrollmentData->take(4) as $index => $course)
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block">{{ Str::limit($course['name'], 15) }}</small>
                                    <span class="custom-badge badge-primary-custom">{{ $course['enrollments'] }} students</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Enrollment Trends -->
            <div class="col-lg-6 mb-4">
                <div class="analytics-card">
                    <div class="analytics-card-header">
                        <h5><i class="fas fa-chart-line"></i> Monthly Enrollment Trends</h5>
                    </div>
                    <div class="analytics-card-body">
                        <div class="chart-container">
                            <canvas id="monthlyEnrollmentChart" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Enrollment Details Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="analytics-table">
                    <div class="analytics-card-header">
                        <h5><i class="fas fa-users"></i> Course Enrollment Details</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Course Name</th>
                                    <th>Created By</th>
                                    <th>Total Enrollments</th>
                                    <th>Enrollment Rate</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($courseEnrollmentData as $index => $course)
                                <tr>
                                    <td><strong>{{ $index + 1 }}</strong></td>
                                    <td>
                                        <strong>{{ $course['name'] }}</strong>
                                    </td>
                                    <td>{{ $course['creator'] }}</td>
                                    <td><span class="custom-badge badge-primary-custom">{{ $course['enrollments'] }}</span></td>
                                    <td>
                                        @php
                                            $enrollmentPercentage = $totalUsers > 0 ? round(($course['enrollments'] / $totalUsers) * 100, 1) : 0;
                                        @endphp
                                        <div class="custom-progress">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ min($enrollmentPercentage, 100) }}%"
                                                 aria-valuenow="{{ $enrollmentPercentage }}" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $enrollmentPercentage }}% of users</small>
                                    </td>
                                    <td><span class="custom-badge badge-success-custom">Active</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No enrollment data available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Activity -->
        <div class="row pb-5">
            <div class="col-12">
                <div class="card system-activity-card">
                    <div class="analytics-card-header">
                        <h5><i class="fas fa-heartbeat"></i> System Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="activity-stat">
                                    <h4>{{ $activeUsersToday }}</h4>
                                    <p class="mb-0 text-muted">Active Users Today</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="activity-stat">
                                    <h4>{{ $newRegistrationsThisWeek }}</h4>
                                    <p class="mb-0 text-muted">New Registrations (Week)</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="activity-stat">
                                    <h4>{{ $coursesCreatedThisMonth }}</h4>
                                    <p class="mb-0 text-muted">Courses Created (Month)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('assets/js/chart.js') }}"></script>
    
    <script>
        // Enhanced Chart.js configuration with modern styling
        Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        Chart.defaults.color = '#6c757d';
        
        // User Distribution Pie Chart
        const userCtx = document.getElementById('userDistributionChart').getContext('2d');
        new Chart(userCtx, {
            type: 'doughnut',
            data: {
                labels: ['Admins', 'Teachers', 'Students'],
                datasets: [{
                    data: [{{ $adminCount }}, {{ $teacherCount }}, {{ $studentCount }}],
                    backgroundColor: [
                        '#06BBCC',  // Primary color from your theme
                        '#28a745',  // Success green
                        '#ffc107'   // Warning yellow
                    ],
                    borderColor: [
                        '#048a99',  // Darker shade of primary
                        '#1e7e34',  // Darker shade of green
                        '#e0a800'   // Darker shade of yellow
                    ],
                    borderWidth: 2,
                    hoverOffset: 10,
                    hoverBackgroundColor: [
                        '#048a99',
                        '#1e7e34', 
                        '#e0a800'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#06BBCC',
                        borderWidth: 1,
                        cornerRadius: 8
                    }
                },
                cutout: '60%'
            }
        });

        // Monthly Registrations Line Chart
        const regCtx = document.getElementById('monthlyRegistrationsChart').getContext('2d');
        const monthlyRegData = @json($monthlyRegistrations);
        const regLabels = monthlyRegData.map(item => {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return months[item.month - 1] + ' ' + item.year;
        });
        const regCounts = monthlyRegData.map(item => item.count);
        
        new Chart(regCtx, {
            type: 'line',
            data: {
                labels: regLabels,
                datasets: [{
                    label: 'New Registrations',
                    data: regCounts,
                    borderColor: '#06BBCC',
                    backgroundColor: 'rgba(6, 187, 204, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#06BBCC',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#06BBCC',
                        borderWidth: 1,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    }
                }
            }
        });

        // Monthly Courses Bar Chart
        const courseCtx = document.getElementById('monthlyCoursesChart').getContext('2d');
        const monthlyCourseData = @json($monthlyCourses);
        const courseLabels = monthlyCourseData.map(item => {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return months[item.month - 1] + ' ' + item.year;
        });
        const courseCounts = monthlyCourseData.map(item => item.count);
        
        new Chart(courseCtx, {
            type: 'bar',
            data: {
                labels: courseLabels,
                datasets: [{
                    label: 'Courses Created',
                    data: courseCounts,
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: '#28a745',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#28a745',
                        borderWidth: 1,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    }
                }
            }
        });
        
        // Course Enrollment Pie Chart
        const enrollmentCtx = document.getElementById('courseEnrollmentChart').getContext('2d');
        const enrollmentData = @json($courseEnrollmentData);
        const enrollmentLabels = enrollmentData.map(item => item.name.length > 15 ? item.name.substring(0, 15) + '...' : item.name);
        const enrollmentCounts = enrollmentData.map(item => item.enrollments);
        
        // Generate colors using CSS variables
        const enrollmentColors = [
            '#06BBCC', '#28a745', '#ffc107', '#17a2b8', 
            '#6c757d', '#181d38', '#048a99', '#1e7e34'
        ];
        
        new Chart(enrollmentCtx, {
            type: 'doughnut',
            data: {
                labels: enrollmentLabels,
                datasets: [{
                    data: enrollmentCounts,
                    backgroundColor: enrollmentColors,
                    borderColor: enrollmentColors.map(color => color + '80'),
                    borderWidth: 2,
                    hoverOffset: 10,
                    hoverBackgroundColor: enrollmentColors.map(color => color + 'CC')
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 11,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#06BBCC',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed * 100) / total).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });

        // Monthly Enrollment Trends Line Chart
        const monthlyEnrollmentCtx = document.getElementById('monthlyEnrollmentChart').getContext('2d');
        const monthlyEnrollmentData = @json($monthlyEnrollments);
        const enrollmentTrendLabels = monthlyEnrollmentData.map(item => {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return months[item.month - 1] + ' ' + item.year;
        });
        const enrollmentTrendCounts = monthlyEnrollmentData.map(item => item.count);
        
        new Chart(monthlyEnrollmentCtx, {
            type: 'line',
            data: {
                labels: enrollmentTrendLabels,
                datasets: [{
                    label: 'New Enrollments',
                    data: enrollmentTrendCounts,
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#28a745',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#28a745',
                        borderWidth: 1,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    }
                }
            }
        });

        // Quiz Submissions Trend Chart
        const quizSubmissionsCtx = document.getElementById('quizSubmissionsChart').getContext('2d');
        const quizSubmissionsData = @json($quizSubmissionsChart);
        const quizLabels = quizSubmissionsData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        const quizCounts = quizSubmissionsData.map(item => item.count);
        const quizAvgScores = quizSubmissionsData.map(item => parseFloat(item.avg_score || 0).toFixed(1));
        
        new Chart(quizSubmissionsCtx, {
            type: 'bar',
            data: {
                labels: quizLabels,
                datasets: [{
                    label: 'Submissions',
                    data: quizCounts,
                    backgroundColor: '#06BBCC',
                    borderColor: '#048a99',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#06BBCC',
                        borderWidth: 1,
                        cornerRadius: 8,
                        callbacks: {
                            afterBody: function(context) {
                                const index = context[0].dataIndex;
                                return 'Avg Score: ' + quizAvgScores[index] + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    }
                }
            }
        });

        // Assignment Submissions Bar Chart
        const assignmentSubmissionsCtx = document.getElementById('assignmentSubmissionsChart').getContext('2d');
        const assignmentSubmissionsData = @json($assignmentSubmissionsChart);
        const assignmentLabels = assignmentSubmissionsData.map(item => {
            const date = new Date(item.date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        const assignmentCounts = assignmentSubmissionsData.map(item => item.count);
        
        new Chart(assignmentSubmissionsCtx, {
            type: 'bar',
            data: {
                labels: assignmentLabels,
                datasets: [{
                    label: 'Submissions',
                    data: assignmentCounts,
                    backgroundColor: '#28a745',
                    borderColor: '#1e7e34',
                    borderWidth: 1,
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#28a745',
                        borderWidth: 1,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6c757d'
                        }
                    }
                }
            }
        });

        // Quiz Score Distribution Pie Chart
        // const quizScoreCtx = document.getElementById('quizScoreDistributionChart').getContext('2d');
        // const quizScoreData = @json($quizScoreDistribution);
        // const scoreLabels = quizScoreData.map(item => item.score_range);
        // const scoreCounts = quizScoreData.map(item => item.count);
        
        // const scoreColors = [
        //     '#28a745',  // Excellent - Green
        //     '#06BBCC',  // Good - Primary
        //     '#ffc107',  // Average - Yellow
        //     '#fd7e14',  // Below Average - Orange
        //     '#dc3545'   // Poor - Red
        // ];
        
        // new Chart(quizScoreCtx, {
        //     type: 'doughnut',
        //     data: {
        //         labels: scoreLabels,
        //         datasets: [{
        //             data: scoreCounts,
        //             backgroundColor: scoreColors,
        //             borderColor: scoreColors.map(color => color + '80'),
        //             borderWidth: 2,
        //             hoverOffset: 8
        //         }]
        //     },
        //     options: {
        //         responsive: true,
        //         maintainAspectRatio: false,
        //         plugins: {
        //             legend: {
        //                 position: 'bottom',
        //                 labels: {
        //                     padding: 15,
        //                     usePointStyle: true,
        //                     font: {
        //                         size: 11,
        //                         weight: '500'
        //                     }
        //                 }
        //             },
        //             tooltip: {
        //                 backgroundColor: 'rgba(0, 0, 0, 0.8)',
        //                 titleColor: '#fff',
        //                 bodyColor: '#fff',
        //                 borderColor: '#06BBCC',
        //                 borderWidth: 1,
        //                 cornerRadius: 8,
        //                 callbacks: {
        //                     label: function(context) {
        //                         const total = context.dataset.data.reduce((a, b) => a + b, 0);
        //                         const percentage = ((context.parsed * 100) / total).toFixed(1);
        //                         return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
        //                     }
        //                 }
        //             }
        //         },
        //         cutout: '60%'
        //     }
        // });
    </script>
</x-adminlayout>