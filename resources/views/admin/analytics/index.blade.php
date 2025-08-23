<x-adminlayout>
    <section class="home container-fluid analytics-dashboard">
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
    </script>
</x-adminlayout>