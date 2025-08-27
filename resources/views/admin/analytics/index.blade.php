<x-adminlayout>
    <section class="home container analytics-dashboard">
        <div class="text my-4">
            <h2><i class="fas fa-chart-line"></i> Learning Analytics Dashboard</h2>
            <p class="text-muted">Comprehensive insights into your learning management system</p>
        </div>

        <!-- Date Filter Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="date-filter-card">
                    <div class="date-filter-header">
                        <h5><i class="fas fa-calendar-alt"></i> Date Range Filter</h5>
                    </div>
                    <div class="date-filter-body">
                        <form method="GET" action="{{ route('admin.analytics') }}" class="date-filter-form">
                            <div class="row align-items-end">
                                <div class="col-md-4 mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control date-input" id="start_date" name="start_date" 
                                           value="{{ request('start_date', now()->subMonths(12)->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control date-input" id="end_date" name="end_date" 
                                           value="{{ request('end_date', now()->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="filter-buttons">
                                        <button type="submit" class="btn btn-filter-apply">
                                            <i class="fas fa-search"></i> Apply Filter
                                        </button>
                                        <a href="{{ route('admin.analytics') }}" class="btn btn-filter-reset">
                                            <i class="fas fa-undo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="quick-filters">
                                        <span class="quick-filter-label">Quick Filters:</span>
                                        <button type="button" class="btn btn-quick-filter" onclick="setDateRange('today')">
                                            Today
                                        </button>
                                        <button type="button" class="btn btn-quick-filter" onclick="setDateRange('week')">
                                            This Week
                                        </button>
                                        <button type="button" class="btn btn-quick-filter" onclick="setDateRange('month')">
                                            This Month
                                        </button>
                                        <button type="button" class="btn btn-quick-filter" onclick="setDateRange('quarter')">
                                            This Quarter
                                        </button>
                                        <button type="button" class="btn btn-quick-filter" onclick="setDateRange('year')">
                                            This Year
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
                        <h5><i class="fas fa-chart-line"></i> Quiz Submissions (Current Month)</h5>
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
                        <h5><i class="fas fa-chart-bar"></i> Assignment Submissions (Current Month)</h5>
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
                        <!-- <div class="mt-3">
                            <div class="row">
                                @foreach($courseEnrollmentData->take(4) as $index => $course)
                                <div class="col-6 mb-2">
                                    <small class="text-muted d-block">{{ Str::limit($course['name'], 15) }}</small>
                                    <span class="custom-badge badge-primary-custom">{{ $course['enrollments'] }} students</span>
                                </div>
                                @endforeach
                            </div>
                        </div> -->
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

        <!-- Monthly Activity Comparison Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="analytics-card">
                    <div class="analytics-card-header">
                        <h5><i class="fas fa-chart-bar"></i> Monthly Activity Comparison</h5>
                        <p class="text-muted mb-0">Course enrollments, quiz submissions, and assignment submissions by month</p>
                    </div>
                    <div class="analytics-card-body">
                        <div class="chart-container" style="height: 400px;">
                            <canvas id="monthlyComparisonChart" class="chart-canvas"></canvas>
                        </div>
                        
                        <!-- Summary Statistics -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <h6 class="mb-1">Total Enrollments</h6>
                                    <span class="custom-badge badge-primary-custom">{{ $monthlyComparison->sum('enrollments') }}</span>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="mb-1">Total Quiz Submissions</h6>
                                    <span class="custom-badge badge-warning-custom">{{ $monthlyComparison->sum('quiz_submissions') }}</span>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="mb-1">Total Assignment Submissions</h6>
                                    <span class="custom-badge badge-info-custom">{{ $monthlyComparison->sum('assignment_submissions') }}</span>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="mb-1">Most Active Month</h6>
                                    @php
                                        $mostActiveMonth = $monthlyComparison->sortByDesc(function($item) {
                                            return $item['enrollments'] + $item['quiz_submissions'] + $item['assignment_submissions'];
                                        })->first();
                                    @endphp
                                    <span class="custom-badge badge-success-custom">{{ $mostActiveMonth['month'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
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

        // Date Range Quick Filter Functions
        function setDateRange(period) {
            const today = new Date();
            let startDate, endDate;
            
            switch(period) {
                case 'today':
                    startDate = endDate = today.toISOString().split('T')[0];
                    break;
                case 'week':
                    const weekStart = new Date(today.setDate(today.getDate() - today.getDay()));
                    startDate = weekStart.toISOString().split('T')[0];
                    endDate = new Date().toISOString().split('T')[0];
                    break;
                case 'month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                    endDate = new Date().toISOString().split('T')[0];
                    break;
                case 'quarter':
                    const quarter = Math.floor(today.getMonth() / 3);
                    startDate = new Date(today.getFullYear(), quarter * 3, 1).toISOString().split('T')[0];
                    endDate = new Date().toISOString().split('T')[0];
                    break;
                case 'year':
                    startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                    endDate = new Date().toISOString().split('T')[0];
                    break;
            }
            
            document.getElementById('start_date').value = startDate;
            document.getElementById('end_date').value = endDate;
        }

        // Auto-submit form when date inputs change
        document.getElementById('start_date').addEventListener('change', function() {
            if (this.value && document.getElementById('end_date').value) {
                document.querySelector('.date-filter-form').submit();
            }
        });

        document.getElementById('end_date').addEventListener('change', function() {
            if (this.value && document.getElementById('start_date').value) {
                document.querySelector('.date-filter-form').submit();
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

        // Monthly Comparison Chart
        const monthlyComparisonCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
        const monthlyComparisonChart = new Chart(monthlyComparisonCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyComparison->pluck('month')) !!},
                datasets: [
                    {
                        label: 'Course Enrollments',
                        data: {!! json_encode($monthlyComparison->pluck('enrollments')) !!},
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                    },
                    {
                        label: 'Quiz Submissions',
                        data: {!! json_encode($monthlyComparison->pluck('quiz_submissions')) !!},
                        backgroundColor: 'rgba(255, 193, 7, 0.8)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                    },
                    {
                        label: 'Assignment Submissions',
                        data: {!! json_encode($monthlyComparison->pluck('assignment_submissions')) !!},
                        backgroundColor: 'rgba(23, 162, 184, 0.8)',
                        borderColor: 'rgba(23, 162, 184, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Monthly Activity Comparison',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#333'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#ddd',
                        borderWidth: 1,
                        cornerRadius: 6,
                        displayColors: true,
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y;
                            },
                            afterBody: function(context) {
                                const total = context.reduce((sum, item) => sum + item.parsed.y, 0);
                                return 'Total Activity: ' + total;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Month',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Count',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    </script>
</x-adminlayout>