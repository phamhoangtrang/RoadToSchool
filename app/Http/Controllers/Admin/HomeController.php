<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillCourse;
use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    protected $modelUser;
    protected $modelBill;
    protected $modelCourse;

    public function __construct(Bill $bill, User $user, Course $course)
    {
        $this->modelBill = $bill;
        $this->modelUser = $user;
        $this->modelCourse = $course;
    }

    public function index(Request $request)
    {
        $preset = $request->get('preset', 'all');
        $startDateInput = $request->get('start_date');
        $endDateInput = $request->get('end_date');

        $startDate = null;
        $endDate = null;
        $filterTitle = 'Tất cả thời gian';

        // Xử lý bộ lọc thời gian
        if (!empty($startDateInput) && !empty($endDateInput)) {
            $startDate = Carbon::parse($startDateInput)->startOfDay();
            $endDate = Carbon::parse($endDateInput)->endOfDay();
            $preset = 'custom';
            $filterTitle = 'Từ ' . $startDate->format('d/m/Y') . ' đến ' . $endDate->format('d/m/Y');
        } elseif ($preset === 'today') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
            $filterTitle = 'Hôm nay (' . $startDate->format('d/m/Y') . ')';
        } elseif ($preset === '7_days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            $filterTitle = '1 tuần qua (' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y') . ')';
        } elseif ($preset === '30_days') {
            // 1 tháng qua = từ cùng ngày của tháng trước đến hôm nay (vd 16/10 - 16/11).
            // Dùng subMonthNoOverflow để ngày 31 không bị nhảy sang tháng sau (31/03 -> 28/02).
            $startDate = Carbon::now()->subMonthNoOverflow()->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            $filterTitle = '1 tháng qua (' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y') . ')';
        } elseif ($preset === 'this_month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            $filterTitle = 'Tháng này (' . $startDate->format('m/Y') . ')';
        } elseif ($preset === 'last_month') {
            $startDate = Carbon::now()->subMonth()->startOfMonth();
            $endDate = Carbon::now()->subMonth()->endOfMonth();
            $filterTitle = 'Tháng trước (' . $startDate->format('m/Y') . ')';
        } elseif ($preset === 'this_year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
            $filterTitle = 'Năm nay (' . $startDate->format('Y') . ')';
        } else {
            $preset = 'all';
            $filterTitle = 'Tất cả thời gian';
        }

        // 1. DOANH THU & ĐƠN HÀNG
        $paidStatus = [Bill::ALREADY_PAID, Bill::ACTIVATED];

        $paidBillsQuery = Bill::whereIn('status', $paidStatus);
        $totalBillsQuery = Bill::query();

        if ($startDate && $endDate) {
            $paidBillsQuery->whereBetween('created_at', [$startDate, $endDate]);
            $totalBillsQuery->whereBetween('created_at', [$startDate, $endDate]);
        }

        $filteredRevenue = (clone $paidBillsQuery)->sum('total_amount');
        $filteredPaidBillsCount = (clone $paidBillsQuery)->count();
        $filteredTotalBillsCount = (clone $totalBillsQuery)->count();

        // Phân chia doanh thu 70% Giảng viên - 30% Sàn (Chi phí vận hành)
        $instructorCost = round($filteredRevenue * 0.70);
        $platformProfit = $filteredRevenue - $instructorCost;

        // 2. SỐ LƯỢNG KHÓA HỌC ĐÃ BÁN (Tính từ các đơn thanh toán thành công)
        $coursesSoldQuery = BillCourse::join('bills', 'bills.id', '=', 'bill_course.bill_id')
            ->whereIn('bills.status', $paidStatus);

        if ($startDate && $endDate) {
            $coursesSoldQuery->whereBetween('bills.created_at', [$startDate, $endDate]);
        }
        $filteredCoursesSold = $coursesSoldQuery->count();

        // 3. KHÁCH HÀNG (Học viên role = 2)
        $totalCustomers = User::where('role', 2)->count();
        $newCustomers = User::where('role', 2)
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('created_at', [$startDate, $endDate]);
            })->count();

        // 4. CHART DATA: Thống kê doanh thu theo thời gian
        $chartLabels = [];
        $chartRevenueData = [];
        $chartCoursesData = [];

        if ($startDate && $endDate) {
            $diffInDays = $startDate->diffInDays($endDate);

            if ($startDate->isSameDay($endDate)) {
                // Chỉ 1 ngày: nhóm theo từng giờ để biểu đồ không bị trống
                $hourlyStats = Bill::whereIn('status', $paidStatus)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->select(DB::raw('HOUR(created_at) as hour_val'), DB::raw('SUM(total_amount) as total_rev'))
                    ->groupBy('hour_val')
                    ->pluck('total_rev', 'hour_val')
                    ->all();

                $hourlyCourses = BillCourse::join('bills', 'bills.id', '=', 'bill_course.bill_id')
                    ->whereIn('bills.status', $paidStatus)
                    ->whereBetween('bills.created_at', [$startDate, $endDate])
                    ->select(DB::raw('HOUR(bills.created_at) as hour_val'), DB::raw('COUNT(*) as total_courses'))
                    ->groupBy('hour_val')
                    ->pluck('total_courses', 'hour_val')
                    ->all();

                for ($h = 0; $h < 24; $h++) {
                    $chartLabels[] = sprintf('%02dh', $h);
                    $chartRevenueData[] = (float)($hourlyStats[$h] ?? 0);
                    $chartCoursesData[] = (int)($hourlyCourses[$h] ?? 0);
                }
            } elseif ($diffInDays <= 31) {
                // Nhóm theo từng ngày
                $period = new \DatePeriod(
                    $startDate->copy()->startOfDay(),
                    new \DateInterval('P1D'),
                    $endDate->copy()->addDay()->startOfDay()
                );

                $dailyStats = Bill::whereIn('status', $paidStatus)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->select(
                        DB::raw('DATE(created_at) as date_val'),
                        DB::raw('SUM(total_amount) as total_rev')
                    )
                    ->groupBy('date_val')
                    ->pluck('total_rev', 'date_val')
                    ->all();

                $dailyCourses = BillCourse::join('bills', 'bills.id', '=', 'bill_course.bill_id')
                    ->whereIn('bills.status', $paidStatus)
                    ->whereBetween('bills.created_at', [$startDate, $endDate])
                    ->select(
                        DB::raw('DATE(bills.created_at) as date_val'),
                        DB::raw('COUNT(*) as total_courses')
                    )
                    ->groupBy('date_val')
                    ->pluck('total_courses', 'date_val')
                    ->all();

                foreach ($period as $dt) {
                    $key = $dt->format('Y-m-d');
                    $chartLabels[] = $dt->format('d/m');
                    $chartRevenueData[] = (float)($dailyStats[$key] ?? 0);
                    $chartCoursesData[] = (int)($dailyCourses[$key] ?? 0);
                }
            } else {
                // Nhóm theo từng tháng
                $monthlyStats = Bill::whereIn('status', $paidStatus)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->select(
                        DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_val'),
                        DB::raw('SUM(total_amount) as total_rev')
                    )
                    ->groupBy('month_val')
                    ->orderBy('month_val', 'asc')
                    ->get();

                $monthlyCourses = BillCourse::join('bills', 'bills.id', '=', 'bill_course.bill_id')
                    ->whereIn('bills.status', $paidStatus)
                    ->whereBetween('bills.created_at', [$startDate, $endDate])
                    ->select(DB::raw('DATE_FORMAT(bills.created_at, "%Y-%m") as month_val'), DB::raw('COUNT(*) as total_courses'))
                    ->groupBy('month_val')
                    ->pluck('total_courses', 'month_val')
                    ->all();

                foreach ($monthlyStats as $ms) {
                    $chartLabels[] = Carbon::createFromFormat('Y-m', $ms->month_val)->format('m/Y');
                    $chartRevenueData[] = (float)$ms->total_rev;
                    $chartCoursesData[] = (int)($monthlyCourses[$ms->month_val] ?? 0);
                }
            }
        } else {
            // Tất cả thời gian: nhóm theo từng tháng có dữ liệu
            $monthlyStats = Bill::whereIn('status', $paidStatus)
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_val'),
                    DB::raw('SUM(total_amount) as total_rev')
                )
                ->groupBy('month_val')
                ->orderBy('month_val', 'asc')
                ->limit(12)
                ->get();

            if ($monthlyStats->isEmpty()) {
                $chartLabels = [Carbon::now()->format('m/Y')];
                $chartRevenueData = [0];
                $chartCoursesData = [0];
            } else {
                $allMonthlyCourses = BillCourse::join('bills', 'bills.id', '=', 'bill_course.bill_id')
                    ->whereIn('bills.status', $paidStatus)
                    ->select(DB::raw('DATE_FORMAT(bills.created_at, "%Y-%m") as month_val'), DB::raw('COUNT(*) as total_courses'))
                    ->groupBy('month_val')
                    ->pluck('total_courses', 'month_val')
                    ->all();

                foreach ($monthlyStats as $ms) {
                    $chartLabels[] = Carbon::createFromFormat('Y-m', $ms->month_val)->format('m/Y');
                    $chartRevenueData[] = (float)$ms->total_rev;
                    $chartCoursesData[] = (int)($allMonthlyCourses[$ms->month_val] ?? 0);
                }
            }
        }

        // 5. Đơn hàng gần đây theo kỳ lọc
        $newestBills = Bill::when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // 6. Top khóa học bán chạy trong kỳ lọc
        $topCourseIdsInPeriod = BillCourse::join('bills', 'bills.id', '=', 'bill_course.bill_id')
            ->whereIn('bills.status', $paidStatus)
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                return $q->whereBetween('bills.created_at', [$startDate, $endDate]);
            })
            ->select('bill_course.course_id', DB::raw('COUNT(*) as period_sales'))
            ->groupBy('bill_course.course_id')
            ->orderBy('period_sales', 'desc')
            ->limit(5)
            ->pluck('period_sales', 'course_id')
            ->all();

        if (!empty($topCourseIdsInPeriod)) {
            $topCoursesInPeriod = Course::whereIn('id', array_keys($topCourseIdsInPeriod))->get()->map(function($course) use ($topCourseIdsInPeriod) {
                $course->period_sales = $topCourseIdsInPeriod[$course->id] ?? 0;
                return $course;
            })->sortByDesc('period_sales');
        } else {
            $topCoursesInPeriod = Course::where('is_accepted', 1)->orderBy('seller', 'desc')->limit(5)->get()->map(function($course) {
                $course->period_sales = 0;
                return $course;
            });
        }

        // 7. Thống kê chung hệ thống
        $count['adminsCount'] = User::where('is_admin', 1)->count();
        $count['teachersCount'] = User::where('role', 1)->count();
        $count['studentsCount'] = User::where('role', 2)->count();
        $count['totalUsers'] = User::count();
        $count['totalCourses'] = Course::where('is_accepted', 1)->count();
        $count['allTimeRevenue'] = Bill::whereIn('status', $paidStatus)->sum('total_amount');
        $count['allTimeCoursesSold'] = BillCourse::join('bills', 'bills.id', '=', 'bill_course.bill_id')
            ->whereIn('bills.status', $paidStatus)->count();

        $excellentInstructors = User::where('role', 1)->orderBy('instructor_rate', 'desc')->limit(5)->get();

        return view('admin.home', compact(
            'preset',
            'startDateInput',
            'endDateInput',
            'filterTitle',
            'filteredRevenue',
            'instructorCost',
            'platformProfit',
            'filteredCoursesSold',
            'filteredPaidBillsCount',
            'filteredTotalBillsCount',
            'totalCustomers',
            'newCustomers',
            'chartLabels',
            'chartRevenueData',
            'chartCoursesData',
            'newestBills',
            'topCoursesInPeriod',
            'count',
            'excellentInstructors'
        ));
    }
}
